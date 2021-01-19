<?php

namespace Tests\Unit\Mail;

use App\Mail\OrderConfirmationEmail;
use App\Models\Order;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderConfirmationEmailTest extends TestCase
{
    /** @test */
    public function emails_contains_a_link_to_the_order_confirmation_page()
    {
        $order = Order::factory()->make([
            'confirmation_number' => 'CONFIRMATIONNUMBER1234'
        ]);

        $email = new OrderConfirmationEmail($order);

        $this->assertStringContainsString(url('/orders/CONFIRMATIONNUMBER1234'), $email->render());
    }
}
