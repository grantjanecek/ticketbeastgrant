<?php

namespace Tests\Unit;

use App\Billing\Charge;
use App\Exceptions\OrderNotFound;
use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function converting_to_an_array()
    {
        $order = Order::factory()
            ->create([
                'confirmation_number' => 'CONFIRMATIONNUMBER123',
                'email' => 'jane@example.com',
                'amount' => 6000,
            ]);
        $order->tickets()->saveMany([
            Ticket::factory()->create(['code' => 'TICKETCODE1']),
            Ticket::factory()->create(['code' => 'TICKETCODE2']),
            Ticket::factory()->create(['code' => 'TICKETCODE3']),
        ]);

        $result = $order->toArray();

        $this->assertEquals([
            'confirmation_number' => 'CONFIRMATIONNUMBER123',
            'email' => 'jane@example.com',
            'amount' => 6000,
            'tickets' => [
                ['code' => 'TICKETCODE1'],
                ['code' => 'TICKETCODE2'],
                ['code' => 'TICKETCODE3'],
            ],
        ], $result);
    }

    /** @test */
    public function creating_an_order_from_tickets_and_email_and_charge()
    {
        $charge =new Charge(['amount' => 3600, 'card_last_four' => '1234']);

        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);

        $order = Order::forTickets($tickets, 'john@example.com', $charge);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals('1234', $order->card_last_four);

        $tickets->each->shouldHaveReceived('claimFor', [$order]);
    }

    /** @test */
    public function retrieving_an_order_by_confirmation_number()
    {
        $order = Order::factory()->create([
            'confirmation_number' => 'CONFIRMATIONNUMBER1234'
        ]);

        $foundOrder = Order::findByConfirmationNumber('CONFIRMATIONNUMBER1234');

        $this->assertEquals($order->id, $foundOrder->id);
    }

    /** @test */
    public function retrieving_on_order_by_confirmation_number_that_doesnt_exist_throws_exception()
    {
        Order::factory()->create([
            'confirmation_number' => 'CONFIRMATIONNUMBER1234'
        ]);

        try {
            Order::findByConfirmationNumber('bad-order-number');

            $this->fail('Expected an order not found exception');
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }
}
