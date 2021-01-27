<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use App\Models\User;
use App\Models\Concert;
use App\Models\AttendeeMessage;
use App\Jobs\SendAttendeeMessage;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessageAttendeesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_promoter_can_view_the_message_form_for_their_own_concert()
    {
        $user = User::factory()->create();

        $concert = Concert::factory()->for($user)->create();

        $response = $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/messages/new");

        $response->assertSuccessful();
        $response->assertViewIs('backstage.concert-messages.create');
        $this->assertTrue($response->viewData('concert')->is($concert));
    }

    /** @test */
    function a_promoter_cannot_view_the_message_form_for_other_cocnerts()
    {
        $user = User::factory()->create();

        $concert = Concert::factory()->for(User::factory())->create();

        $response = $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/messages/new");

        $response->assertStatus(404);
    }

    /** @test */
    function a_guest_cannot_view_the_message_form()
    {
        $concert = Concert::factory()->create();

        $response = $this->get("/backstage/concerts/{$concert->id}/messages/new");

        $response->assertRedirect('/login');
    }

    /** @test */
    function a_promoter_can_send_a_new_message()
    {
        Queue::fake();
        $user = User::factory()->create();

        $concert = Concert::factory()->published()->for($user)->create();

        $response = $this->actingAs($user)->post("/backstage/concerts/{$concert->id}/messages", [
            'subject' => 'My Subject',
            'message' => 'My Message',
        ]);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/messages/new");
        $response->assertSessionHas('flash');

        $message = AttendeeMessage::first();

        $this->assertEquals($concert->id, $message->concert->id);
        $this->assertEquals('My Subject', $message->subject);
        $this->assertEquals('My Message', $message->message);

        Queue::assertPushed(SendAttendeeMessage::class, fn($job) => $job->attendeeMessage->is($message));
    }

    /** @test */
    function a_promoter_cannot_send_a_message_for_other_concerts()
    {
        Queue::fake();
        $user = User::factory()->create();

        $concert = Concert::factory()->published()->for(User::factory())->create();

        $response = $this->actingAs($user)->post("/backstage/concerts/{$concert->id}/messages");

        $response->assertStatus(404);
        $this->assertDatabaseCount('attendee_messages', 0);
        Queue::assertNotPushed(SendAttendeeMessage::class);
    }

    /** @test */
    function a_guest_cannot_send_a_messages()
    {
        Queue::fake();
        $concert = Concert::factory()->published()->create();

        $response = $this->post("/backstage/concerts/{$concert->id}/messages");

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('attendee_messages', 0);
        Queue::assertNotPushed(SendAttendeeMessage::class);
    }

    /** @test */
    function subject_is_required_to_send_a_message()
    {
        Queue::fake();
        $user = User::factory()->create();

        $concert = Concert::factory()->published()->for($user)->create();

        $response = $this->from("/backstage/concerts/{$concert->id}/messages/new")->actingAs($user)->post("/backstage/concerts/{$concert->id}/messages", [
            'subject' => '',
            'message' => 'My Message',
        ]);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/messages/new");
        $response->assertSessionHasErrors('subject');

        $this->assertDatabaseCount('attendee_messages', 0);
        Queue::assertNotPushed(SendAttendeeMessage::class);
    }

    /** @test */
    function message_is_required_to_send_a_message()
    {
        Queue::fake();
        $user = User::factory()->create();

        $concert = Concert::factory()->published()->for($user)->create();

        $response = $this->from("/backstage/concerts/{$concert->id}/messages/new")->actingAs($user)->post("/backstage/concerts/{$concert->id}/messages", [
            'subject' => 'My Subject',
            'message' => '',
        ]);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/messages/new");
        $response->assertSessionHasErrors('message');

        $this->assertDatabaseCount('attendee_messages', 0);
        Queue::assertNotPushed(SendAttendeeMessage::class);
    }
}
