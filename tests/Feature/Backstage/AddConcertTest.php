<?php

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Concert;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddConcertTest extends TestCase
{
    use RefreshDatabase;

    public function validParams($overrides = [])
    {
        return array_merge([
            'title' => 'No Warning',
            'subtitle' => 'with Cruel Hand and Backtrack',
            'additional_information' => "You must be 19 years of age to attend this concert.",
            'date' => '2017-11-18',
            'time' => '8:00pm',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Fake St.',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '12345',
            'ticket_price' => '32.50',
            'ticket_quantity' => '75',
        ], $overrides);
    }

    /** @test */
    public function promoters_can_view_the_add_concert_form()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/backstage/concerts/new')
            ->assertSuccessful();
    }

    /** @test */
    public function guests_cannot_view_the_add_concert_form()
    {
        $this->get('/backstage/concerts/new')
            ->assertRedirect('/login');
    }

    /** @test */
    public function adding_a_valid_concert()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/backstage/concerts/new', [
            'title' => 'No Warning',
            'subtitle' => 'with Cruel Hand and Backtrack',
            'additional_information' => "You must be 19 years of age to attend this concert.",
            'date' => '2017-11-18',
            'time' => '8:00pm',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Fake St.',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '12345',
            'ticket_price' => '32.50',
            'ticket_quantity' => '75',
        ]);

        tap(Concert::first(), function ($concert) use ($response, $user) {
            $response->assertRedirect("/backstage/concerts/");

            $this->assertTrue($concert->user->is($user));

            $this->assertFalse($concert->isPublished());

            $this->assertEquals('No Warning', $concert->title);
            $this->assertEquals('with Cruel Hand and Backtrack', $concert->subtitle);
            $this->assertEquals("You must be 19 years of age to attend this concert.", $concert->additional_information);
            $this->assertEquals(Carbon::parse('2017-11-18 8:00pm'), $concert->date);
            $this->assertEquals('The Mosh Pit', $concert->venue);
            $this->assertEquals('123 Fake St.', $concert->venue_address);
            $this->assertEquals('Laraville', $concert->city);
            $this->assertEquals('ON', $concert->state);
            $this->assertEquals('12345', $concert->zip);
            $this->assertEquals(3250, $concert->ticket_price);
            $this->assertEquals(75, $concert->ticket_quantity);
            $this->assertEquals(0, $concert->ticketsRemaining());
        });
    }

    /** @test */
    public function guests_cannot_add_a_new_concert()
    {
        $response = $this->post('/backstage/concerts/new', $this->validParams());

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function title_is_required()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams([ 'title' => '']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('title');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function subtitle_is_optional()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/backstage/concerts/new', $this->validParams(['subtitle' => '' ]));

        tap(Concert::first(), function ($concert) use ($response, $user) {
            $response->assertRedirect("/backstage/concerts/");
            $this->assertTrue($concert->user->is($user));

            $this->assertNull($concert->subtitle);
        });
    }

    /** @test */
    public function additional_information_is_optional()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/backstage/concerts/new', $this->validParams(['additional_information' => '']));

        tap(Concert::first(), function ($concert) use ($response, $user) {
            $response->assertRedirect("/backstage/concerts/");
            $this->assertTrue($concert->user->is($user));

            $this->assertNull($concert->additional_information);
        });
    }

    /** @test */
    public function date_is_required()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['date' => '']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('date');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function date_must_be_valid()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['date' => 'not a date']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('date');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function time_is_required()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['time' => '']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('time');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function time_must_be_valid()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['time' => 'not a time']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('time');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function venue_is_required()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['venue' => '']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('venue');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function venue_address_is_required()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['venue_address' => '']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('venue_address');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function city_is_required()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['city' => '']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('city');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function state_is_required()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['state' => '']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('state');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function zip_is_required()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['zip' => '']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('zip');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function ticket_price_is_required()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['ticket_price' => '']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('ticket_price');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function ticket_price_is_numeric()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['ticket_price' => 'not a price']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('ticket_price');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function ticket_price_at_least_5()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['ticket_price' => '4.99']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('ticket_price');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function ticket_quantity_is_required()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['ticket_quantity' => '']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('ticket_quantity');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function ticket_quantity_is_numeric()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['ticket_quantity' => '']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('ticket_quantity');
        $this->assertDatabaseCount('concerts', 0);
    }

    /** @test */
    public function ticket_quantity_is_at_least_1()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/new')->post('/backstage/concerts/new', $this->validParams(['ticket_quantity' => 'o']));

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('ticket_quantity');
        $this->assertDatabaseCount('concerts', 0);
    }
}
