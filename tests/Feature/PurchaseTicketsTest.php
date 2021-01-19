<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\Concert;
use App\Facades\TicketCode;
use Faker\Provider\Payment;
use App\Billing\PaymentGateway;
use App\Billing\FakePaymentGateway;
use App\Mail\OrderConfirmationEmail;
use Illuminate\Support\Facades\Mail;
use App\Facades\OrderConfirmationNumber;
use Illuminate\Foundation\Testing\WithFaker;
use App\Facades\OrderConfirmationNumberGenerator;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    private function orderTickets($concert, $params)
    {
        return $this->postJson("concerts/{$concert->id}/orders", $params);
    }

    /** @test */
    public function customer_can_purchase_tickets_to_a_published_concert()
    {
        $this->withoutExceptionHandling();
        Mail::fake();

        OrderConfirmationNumber::shouldReceive('generate')->andReturn('CONFIRMATIONNUMBER123');
        TicketCode::shouldReceive('generateFor')->andReturn('TICKETCODE1', 'TICKETCODE2', 'TICKETCODE3');

        $concert = Concert::factory()->published()->hasTickets(3)->create(['ticket_price' => 3250]);

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ])->assertSuccessful()
            ->assertJson([
                    'confirmation_number' => 'CONFIRMATIONNUMBER123',
                    'email' => 'john@example.com',
                    'amount' => 9750,
                    'tickets' => [
                        ['code' => 'TICKETCODE1'],
                        ['code' => 'TICKETCODE2'],
                        ['code' => 'TICKETCODE3'],
                    ],
                ]);

        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());

        Mail::assertSent(OrderConfirmationEmail::class, fn($mail) =>
            $mail->hasTo('john@example.com') && $mail->order->id == $order->id
        );
    }

    /** @test */
    public function cannot_purchase_tickets_to_an_unpublished_concert()
    {
        $concert = Concert::factory()
            ->unpublished()
            ->hasTickets()
            ->create();

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ])->assertNotFound();

        $this->assertEquals(0, $concert->orders()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /** @test */
    public function an_order_is_not_created_if_payment_fails()
    {
        $concert = Concert::factory()
            ->published()
            ->hasTickets(3)
            ->create(['ticket_price' => 3250]);

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-token',
        ])->assertStatus(422);

        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNull($order);
        $this->assertEquals(3, $concert->ticketsRemaining());
    }

    /** @test */
    public function cannot_purchase_more_tickets_than_remain()
    {
        $concert = Concert::factory()->published()->hasTickets(50)->create();

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 51,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ])->assertStatus(422);

        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNull($order);
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    public function cannot_purchase_tickets_another_customer_is_already_trying_to_purchase()
    {
        $concert = Concert::factory()->published()->hasTickets(3)->create(['ticket_price' => 1200]);

        $this->paymentGateway->beforeFirstCharge(function () use ($concert) {
            $requestA = $this->app['request'];

            $this->orderTickets($concert, [
                'email' => 'personB@example.com',
                'ticket_quantity' => 1,
                'payment_token' => $this->paymentGateway->getValidTestToken()
            ])->assertStatus(422);


            $this->app['request'] = $requestA;

            $this->assertNull($concert->orders()->where('email', 'personB@example.com')->first());
            $this->assertEquals(0, $this->paymentGateway->totalCharges());
        });

        $this->orderTickets($concert, [
            'email' => 'personA@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ])->assertSuccessful();

        $order = $concert->fresh()->orders()->where('email', 'personA@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());
    }

    /** @test */
    public function email_is_required()
    {
        $concert = Concert::factory()->published()->hasTickets(3)->create();

        $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    public function email_must_be_a_valid_email()
    {
        $concert = Concert::factory()->published()->hasTickets(3)->create();

        $this->orderTickets($concert, [
            'email' => 'not-an-email',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    public function ticket_quantity_is_required()
    {
        $concert = Concert::factory()->published()->hasTickets(3)->create();

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ])->assertJsonValidationErrors('ticket_quantity');
    }

    /** @test */
    public function ticket_quantity_must_be_at_least_1()
    {
        $concert = Concert::factory()->published()->hasTickets(3)->create();

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ])->assertJsonValidationErrors('ticket_quantity');
    }

    /** @test */
    public function payment_token_is_required()
    {
        $concert = Concert::factory()->published()->hasTickets(3)->create();

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
        ])->assertJsonValidationErrors('payment_token');
    }
}
