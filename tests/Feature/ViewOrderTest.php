<?php

use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_their_order()
    {
        $concert = Concert::factory()->create([
            'title' => 'A Red Cord',
            'subtitle' => 'with Animosity and Lethargy',
            'venue' => 'The Example Theater',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17324',
            'date' => Carbon::parse('2021-03-03 20:00')
        ]);
        $order = Order::factory()->create([
            'confirmation_number' => 'CONFIRMATIONNUMBER1234',
            'amount' => '8500',
            'card_last_four' => '1881',
            'email' => 'john@example.com',
        ]);

        Ticket::factory()->create([
            'order_id' => $order->id,
            'concert_id' => $concert->id,
            'code' => 'TICKET123'
        ]);

        Ticket::factory()->create([
            'order_id' => $order->id,
            'concert_id' => $concert->id,
            'code' => 'TICKET456'
        ]);

        $response = $this->get("/orders/CONFIRMATIONNUMBER1234");

        $response->assertSuccessful();
        $response->assertViewHas('order', $order);
        $response->assertSee('CONFIRMATIONNUMBER1234');
        $response->assertSee('$85.00');
        $response->assertSee('**** **** **** 1881');
        $response->assertSee('TICKET123');
        $response->assertSee('TICKET456');
        $response->assertSee('A Red Cord');
        $response->assertSee('with Animosity and Lethargy');
        $response->assertSee('The Example Theater');
        $response->assertSee('123 Example Lane');
        $response->assertSee('Laraville, ON');
        $response->assertSee('17324');
        $response->assertSee('john@example.com');
        $response->assertSee('2021-03-03 20:00');
    }
}
