<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Invitation;
use App\Mail\InvitationEmail;
use App\Facades\InvitationCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitePromoterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function inviting_a_promiter_via_the_cli()
    {
        Mail::fake();

        InvitationCode::shouldReceive('generate')->andReturn('TESTCODE1234');

        $this->artisan('invite-promoter', ['email' => 'jane@example.com']);

        $this->assertDatabaseCount('invitations', 1);
        $invitation = Invitation::first();
        $this->assertEquals('jane@example.com', $invitation->email);
        $this->assertEquals('TESTCODE1234', $invitation->code);

        Mail::assertSent(InvitationEmail::class, fn($email) =>
            $email->hasTo('jane@example.com') && $email->invitation->is($invitation)
        );
    }
}
