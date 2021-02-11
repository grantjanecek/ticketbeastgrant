<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcceptInvitationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function viewing_an_unused_invitation()
    {
        $this->withoutExceptionHandling();

        $invitation = Invitation::factory()->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->get('/invitations/TESTCODE1234');

        $response->assertStatus(200);
        $response->assertViewIs('invitations.show');
        $this->assertTrue($response->viewData('invitation')->is($invitation));
    }

    /** @test */
    function viewing_a_used_invitation()
    {
        $invitation = Invitation::factory()
            ->for(User::factory())
            ->create([
                'code' => 'TESTCODE1234',
            ]);

        $response = $this->get('/invitations/TESTCODE1234');
        $response->assertStatus(404);
    }

    /** @test */
    function viewing_an_invitation_that_does_not_exist()
    {
        $response = $this->get('/invitations/bad-test-code');
        $response->assertStatus(404);
    }

    /** @test */
    function registering_with_a_valid_invitation_code()
    {
        $invitation = Invitation::factory()->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234',
        ]);

        $response->assertRedirect('/backstage/concerts');
        $this->assertDatabaseCount('users', 1);
        $user = User::first();
        $this->assertAuthenticatedAs($user);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('secret', $user->password));

        $this->assertTrue($invitation->fresh()->user->is($user));
    }

    /** @test */
    function registering_with_a_used_invitation_code()
    {
        $invitation = Invitation::factory()->for(User::factory())->create([
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseCount('users', 1);
    }

    /** @test */
    function registering_with_an_invitation_code_that_does_not_exist()
    {
        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'invalid-code',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseCount('users', 0);
    }

    /** @test */
    function email_is_required()
    {
        $invitation = Invitation::factory()->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->from('/invitations/TESTCODE1234')->post('/register', [
            'email' => '',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234',
        ]);

        $response->assertRedirect('/invitations/TESTCODE1234');
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    function email_must_be_an_email()
    {
        $invitation = Invitation::factory()->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->from('/invitations/TESTCODE1234')->post('/register', [
            'email' => 'not-an-email',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234',
        ]);

        $response->assertRedirect('/invitations/TESTCODE1234');
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    function email_must_be_unique()
    {
        $existingUser = User::factory()->create([
            'email' => 'john@example.com',
        ]);
        $this->assertDatabaseCount('users', 1);

        $invitation = Invitation::factory()->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->from('/invitations/TESTCODE1234')->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234',
        ]);

        $response->assertRedirect('/invitations/TESTCODE1234');
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    /** @test */
    function password_is_required()
    {
        $invitation = Invitation::factory()->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->from('/invitations/TESTCODE1234')->post('/register', [
            'email' => 'john@example.com',
            'password' => '',
            'invitation_code' => 'TESTCODE1234',
        ]);

        $response->assertRedirect('/invitations/TESTCODE1234');
        $response->assertSessionHasErrors('password');
    }
}
