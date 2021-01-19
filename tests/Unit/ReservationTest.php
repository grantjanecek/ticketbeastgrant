<?php


namespace Tests\Unit;


use App\Billing\FakePaymentGateway;
use App\Models\Concert;
use App\Models\Ticket;
use App\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegrationAssertPostConditions;
use Mockery\Adapter\Phpunit\MockeryTestCaseSetUp;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    use RefreshDatabase;

    /** @test */
    function calculating_the_total_cost()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'jane@example.com');

        $this->assertEquals(3600, $reservation->totalCost());
    }

    /** @test */
    function it_can_return_tickets()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'jane@example.com');

        $this->assertEquals($tickets, $reservation->tickets());
    }

    /** @test */
    function it_can_return_the_email()
    {
        $reservation = new Reservation(collect(), 'jane@example.com');

        $this->assertEquals('jane@example.com', $reservation->email());
    }

    /** @test */
    function reserved_tickets_are_released_when_a_reservation_is_canceled()
    {
        $tickets = collect([
            \Mockery::spy(Ticket::class),
            \Mockery::spy(Ticket::class),
            \Mockery::spy(Ticket::class),
        ]);
        $reservation = new Reservation($tickets, 'jane@example.com');

        $reservation->cancel();

        foreach ($tickets as $ticket){
            $ticket->shouldHaveReceived('release');
        }
    }

    /** @test */
    function completing_a_reservation()
    {
        $concert = Concert::factory()->has(Ticket::factory()->count(3))->create(['ticket_price' => 1200]);
        $reservation = new Reservation($concert->tickets, 'jane@example.com');
        $paymentGateway = new FakePaymentGateway();

        $order = $reservation->complete($paymentGateway, $paymentGateway->getValidTestToken());

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(3, $order->tickets()->count());
        $this->assertEquals(3600, $paymentGateway->totalCharges());
    }
}
