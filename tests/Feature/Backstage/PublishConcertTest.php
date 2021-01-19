<?php

namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\Ticket;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublishConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_promoter_can_publish_their_own_concert()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->for($user)->create([
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/backstage/publish-concert', [
            'concert_id' => $concert->id,
        ]);

        $response->assertRedirect('/backstage/concerts');
        $concert = $concert->fresh();
        $this->assertTrue($concert->isPublished());
        $this->assertEquals(3, $concert->ticketsRemaining());
    }

    /** @test */
    function a_concert_can_only_be_published_once()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->published()->has(Ticket::factory(3))->for($user)->create([
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/backstage/publish-concert', [
            'concert_id' => $concert->id,
        ]);

        $response->assertStatus(422);
        $this->assertEquals(3, $concert->fresh()->ticketsRemaining());
    }

    /** @test */
    function guests_cannot_publish_concerts()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->for($user)->create([
            'ticket_quantity' => 3,
        ]);

        $response = $this->post('/backstage/publish-concert', [
            'concert_id' => $concert->id,
        ]);

        $response->assertRedirect('/login');
        $concert = $concert->fresh();
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    function promoters_cannot_publish_other_concerts()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->for(User::factory())->create();

        $response = $this->actingAs($user)->post('/backstage/publish-concert', [
            'concert_id' => $concert->id,
        ]);

        $response->assertStatus(404);
        $concert = $concert->fresh();
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    function concert_id_is_required_to_publish_a_concert()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->for($user)->create();

        $response = $this->actingAs($user)->from('/backstage/publish-concert')->post('/backstage/publish-concert', [
            'concert_id' => '',
        ]);

        $response->assertRedirect('/backstage/publish-concert');
        $concert = $concert->fresh();
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    function if_concert_does_not_exist_status_404_is_returned()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/publish-concert')->post('/backstage/publish-concert', [
            'concert_id' => '999',
        ]);

        $response->assertStatus(404);
    }
}
