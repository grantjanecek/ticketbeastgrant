<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Concert;
use App\Models\AttendeeMessage;
use App\Jobs\SendAttendeeMessage;
use App\Mail\AttendeeMessageEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendAttendeeMessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_sends_the_message_to_all_concert_attendees()
    {
        Mail::fake();
        $concert = Concert::factory()->published()->create();
        $otherConcert = Concert::factory()->published()->create();
        $message = AttendeeMessage::factory()->for($concert)->create([
            'subject' => 'My subject',
            'message' => 'My message',
        ]);

        Order::factory()->hasTickets(1, ['concert_id' => $concert->id])->create(['email' => 'alex@example.com']);
        Order::factory()->hasTickets(1, ['concert_id' => $otherConcert->id])->create(['email' => 'jane@example.com']);
        Order::factory()->hasTickets(1, ['concert_id' => $concert->id])->create(['email' => 'taylor@example.com']);
        Order::factory()->hasTickets(1, ['concert_id' => $concert->id])->create(['email' => 'grant@example.com']);

        SendAttendeeMessage::dispatch($message);

        Mail::assertQueued(AttendeeMessageEmail::class, fn($mail) =>
            $mail->hasTo('alex@example.com') && $mail->attendeeMessage->is($message)
        );
        Mail::assertQueued(AttendeeMessageEmail::class, fn($mail) =>
            $mail->hasTo('taylor@example.com') && $mail->attendeeMessage->is($message)
        );
        Mail::assertQueued(AttendeeMessageEmail::class, fn($mail) =>
            $mail->hasTo('grant@example.com') && $mail->attendeeMessage->is($message)
        );
        Mail::assertNotQueued(AttendeeMessageEmail::class, fn($mail) =>
            $mail->hasTo('jane@example.com')
        );

    }
}
