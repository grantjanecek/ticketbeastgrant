<?php

namespace Tests\Feature\Backstage;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Concert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ViewPublishedConcertOrdersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_promoter_can_view_the_orders_of_their_own_published_concert()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $concert = Concert::factory()->published()->for($user)->create();

        $response = $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders");

        $response->assertSuccessful();
        $response->assertViewIs('backstage.published-concert-orders.index');
        $this->assertTrue($response->viewData('concert')->is($concert));
    }

    /** @test */
    function a_promoter_can_view_the_10_most_recent_orders_for_their_concert()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->published()->for($user)->create();

        $oldOrder = Order::factory()->hasTickets(1, ['concert_id' => $concert->id])->create(['created_at' => Carbon::parse('11 days ago')]);

        $recentOrders = Order::factory()
            ->count(10)
            ->hasTickets(1, ['concert_id' => $concert->id])
            ->state(new Sequence(
                ['created_at' => Carbon::parse('1 days ago')],
                ['created_at' => Carbon::parse('2 days ago')],
                ['created_at' => Carbon::parse('3 days ago')],
                ['created_at' => Carbon::parse('4 days ago')],
                ['created_at' => Carbon::parse('5 days ago')],
                ['created_at' => Carbon::parse('6 days ago')],
                ['created_at' => Carbon::parse('7 days ago')],
                ['created_at' => Carbon::parse('8 days ago')],
                ['created_at' => Carbon::parse('9 days ago')],
                ['created_at' => Carbon::parse('10 days ago')],
            ))
            ->create();

        $response = $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders");

        $response->viewData('orders')->assertEquals($recentOrders);
        $response->viewData('orders')->assertNotContains($oldOrder);
    }

    /** @test */
    function a_promoter_cannot_view_the_orders_of_an_unpublished_concert()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->for($user)->create();

        $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders")
            ->assertStatus(404);
    }

    /** @test */
    function a_promoter_cannot_view_the_orders_of_another_users_concert()
    {
        $concert = Concert::factory()->published()->create();

        $this->actingAs(User::factory()->create())->get("/backstage/published-concerts/{$concert->id}/orders")
            ->assertStatus(404);
    }

    /** @test */
    function guests_cannot_view_the_ortders_for_a_concert()
    {
        $concert = Concert::factory()->published()->create();

        $this->get("/backstage/published-concerts/{$concert->id}/orders")
            ->assertRedirect('/login');
    }
}
