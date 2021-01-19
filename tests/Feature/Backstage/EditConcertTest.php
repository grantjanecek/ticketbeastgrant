<?php

namespace Tests\Feature\Backstage;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Concert;
use Illuminate\Foundation\Testing\WithFaker;
use DMS\PHPUnitExtensions\ArraySubset\Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditConcertTest extends TestCase
{
    use RefreshDatabase;

    public function oldAttributes($overrides = [])
    {
        return array_merge([
            'title' => 'old title',
            'subtitle' => 'old subtitle',
            'date' => Carbon::parse('2017-01-01 17:00'),
            'venue' => 'old venue',
            'venue_address' => 'old address',
            'city' => 'old city',
            'state' => 'old state',
            'zip' => '00000',
            'additional_information' => 'old info',
            'ticket_price' => 2000,
            'ticket_quantity' => 5,
        ], $overrides);
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'title' => 'new title',
            'subtitle' => 'new subtitle',
            'date' => '2018-12-12',
            'time' => '3:00pm',
            'venue' => 'new venue',
            'venue_address' => 'new address',
            'city' => 'new city',
            'state' => 'new state',
            'zip' => '99999',
            'additional_information' => 'new info',
            'ticket_price' => '72.50',
            'ticket_quantity' => 1,
        ], $overrides);
    }

    /** @test */
    public function promoters_can_view_the_edit_form_for_their_own_unpublished_concerts()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create(['user_id' => $user->id]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/edit")
                ->assertSuccessful();

            $this->assertTrue($response->viewData('concert')->is($concert));
    }

    /** @test */
    public function promoters_cannot_view_the_edit_form_for_their_own_published_concerts()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->published()->create(['user_id' => $user->id]);
        $this->assertTrue($concert->isPublished());

        $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/edit")
            ->assertStatus(403);
    }

    /** @test */
    public function promoters_cannot_view_the_edit_form_for_other_concerts()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $concert = Concert::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/edit")
            ->assertStatus(404);
    }

    /** @test */
    public function promoters_see_a_404_when_attempting_to_view_the_edit_form_for_a_concert_that_does_not_exist()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get("/backstage/concerts/999/edit")
                ->assertStatus(404);
    }

    /** @test */
    public function guests_are_asked_to_login_when_attempting_to_view_the_edit_form_for_any_concert()
    {
            $concert = Concert::factory()->published()->create();

            $this->get("/backstage/concerts/{$concert->id}/edit")
                ->assertRedirect('/login');
    }

    /** @test */
    function guests_are_asked_to_login_when_attempting_to_view_the_edit_form_for_a_concert_that_does_not_exist()
    {
        $this->get("/backstage/concerts/999/edit")
            ->assertRedirect('/login');
    }

    /** @test */
    public function promoters_can_edit_their_own_unpublished_concerts()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'title' => 'old title',
            'subtitle' => 'old subtitle',
            'date' => Carbon::parse('2017-01-01 17:00'),
            'venue' => 'old venue',
            'venue_address' => 'old address',
            'city' => 'old city',
            'state' => 'old state',
            'zip' => '00000',
            'additional_information' => 'old info',
            'ticket_price' => 2000,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());

        $this->actingAs($user)->patch("/backstage/concerts/{$concert->id}/edit", [
            'user_id' => $user->id,
            'title' => 'new title',
            'subtitle' => 'new subtitle',
            'date' => '2018-12-12',
            'time' => '3:00pm',
            'venue' => 'new venue',
            'venue_address' => 'new address',
            'city' => 'new city',
            'state' => 'new state',
            'zip' => '99999',
            'additional_information' => 'new info',
            'ticket_price' => '72.50',
            'ticket_quantity' => '10',
        ])->assertRedirect('/backstage/concerts');

        tap($concert->fresh(), function($concert){
            $this->assertEquals('new title', $concert->title);
            $this->assertEquals('new subtitle', $concert->subtitle);
            $this->assertEquals('new info', $concert->additional_information);
            $this->assertEquals(Carbon::parse('2018-12-12 3:00pm'), $concert->date);
            $this->assertEquals('new venue', $concert->venue);
            $this->assertEquals('new address', $concert->venue_address);
            $this->assertEquals('new city', $concert->city);
            $this->assertEquals('new state', $concert->state);
            $this->assertEquals('99999', $concert->zip);
            $this->assertEquals('7250', $concert->ticket_price);
            $this->assertEquals(10, $concert->ticket_quantity);
        });
    }

    /** @test */
    public function promoters_cannot_edit_other_unpublished_concerts()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $concert = Concert::factory()->unpublished()->for($otherUser)->create($this->oldAttributes());
        $this->assertFalse($concert->isPublished());

        $this->actingAs($user)->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams())->assertStatus(404);

        Assert::assertArraySubset($this->oldAttributes([
            'user_id' => $otherUser->id
        ]), $concert->fresh()->getAttributes());
    }

    /** @test */
    public function promoters_cannot_edit_published_concerts()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->for($user)->published()->create($this->oldAttributes());
        $this->assertTrue($concert->isPublished());

        $this->actingAs($user)->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams())->assertStatus(403);

        Assert::assertArraySubset($this->oldAttributes([
            'user_id' => $user->id
        ]), $concert->fresh()->getAttributes());
    }

    /** @test */
    public function guests_cannot_edit_concerts()
    {
        $concert = Concert::factory()->unpublished()->create($this->oldAttributes());
        $this->assertFalse($concert->isPublished());

        $this->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams())->assertRedirect('/login');

        Assert::assertArraySubset($this->oldAttributes(), $concert->fresh()->getAttributes());
    }

    /** @test */
    public function title_is_required()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'title' => 'old title',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['title' => '']))
            ->assertStatus(302);

        tap($concert->fresh(), function($concert){
            $this->assertEquals('old title', $concert->title);
        });

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function subtitle_is_optional()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'subtitle' => 'old subtitle',
        ]);
        $this->assertFalse($concert->isPublished());

        $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['subtitle' => '']))
            ->assertRedirect("/backstage/concerts");

        tap($concert->fresh(), function($concert){
            $this->assertNull($concert->subtitle);
        });
    }

    /** @test */
    public function additional_information_is_optional()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'additional_information' => 'old info',
        ]);
        $this->assertFalse($concert->isPublished());

        $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['additional_information' => '']))
            ->assertRedirect("/backstage/concerts");

        tap($concert->fresh(), function($concert){
            $this->assertNull($concert->additional_information);
        });
    }

    /** @test */
    public function date_is_required()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'date' => Carbon::parse('2012-01-09 13:00'),
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['date' => '']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('date');
        tap($concert->fresh(), function($concert){
            $this->assertEquals(Carbon::parse('2012-01-09 13:00'), $concert->date);
        });
    }

    /** @test */
    public function date_is_valid()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'date' => Carbon::parse('2012-01-09 13:00'),
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['date' => 'not a date']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('date');
        tap($concert->fresh(), function($concert){
            $this->assertEquals(Carbon::parse('2012-01-09 13:00'), $concert->date);
        });
    }

    /** @test */
    public function time_is_required()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'date' => Carbon::parse('2012-01-09 13:00'),
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['time' => '']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('time');
        tap($concert->fresh(), function($concert){
            $this->assertEquals(Carbon::parse('2012-01-09 13:00'), $concert->date);
        });
    }

    /** @test */
    public function time_is_valid()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'date' => Carbon::parse('2012-01-09 13:00'),
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['time' => 'not a time']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('time');
        tap($concert->fresh(), function($concert){
            $this->assertEquals(Carbon::parse('2012-01-09 13:00'), $concert->date);
        });
    }

    /** @test */
    public function venue_is_required()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'venue' => 'The Mosh Pit',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['venue' => '']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('venue');

        tap($concert->fresh(), function($concert){
            $this->assertEquals('The Mosh Pit', $concert->venue);
        });
    }

    /** @test */
    public function venue_address_is_required()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'venue_address' => '123 Example Lane',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['venue_address' => '']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('venue_address');
        tap($concert->fresh(), function($concert){
            $this->assertEquals('123 Example Lane', $concert->venue_address);
        });
    }

    /** @test */
    public function city_is_required()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'city' => 'Laraville'
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['city' => '']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('city');
        tap($concert->fresh(), function($concert){
            $this->assertEquals('Laraville', $concert->city);
        });
    }

    /** @test */
    public function state_is_required()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'state' => 'NC',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['state' => '']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('state');
        tap($concert->fresh(), function($concert){
            $this->assertEquals('NC', $concert->state);
        });
    }

    /** @test */
    public function zip_is_required()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'zip' => '12345',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['zip' => '']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('zip');

        tap($concert->fresh(), function($concert){
            $this->assertEquals('12345', $concert->zip);
        });
    }

    /** @test */
    public function ticket_price_is_required()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'ticket_price' => '1000',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['ticket_price' => '']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('ticket_price');
        tap($concert->fresh(), function($concert){
            $this->assertEquals('1000', $concert->ticket_price);
        });
    }

    /** @test */
    public function ticket_price_is_numeric()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'ticket_price' => '1000',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['ticket_price' => 'not a number']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('ticket_price');
        tap($concert->fresh(), function($concert){
            $this->assertEquals('1000', $concert->ticket_price);
        });
    }

    /** @test */
    public function ticket_price_is_at_least_5()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'ticket_price' => '1000'
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['ticket_price' => '4.99']))
            ->assertStatus(302);


        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('ticket_price');
        tap($concert->fresh(), function($concert){
            $this->assertEquals('1000', $concert->ticket_price);
        });
    }

    /** @test */
    public function ticket_quantity_is_required()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['ticket_quantity' => '']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('ticket_quantity');
        tap($concert->fresh(), function($concert){
            $this->assertEquals(5, $concert->ticket_quantity);
        });
    }

    /** @test */
    public function ticket_quantity_must_be_an_integer()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['ticket_quantity' => '7.8']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('ticket_quantity');
        tap($concert->fresh(), function($concert){
            $this->assertEquals(5, $concert->ticket_quantity);
        });
    }

    /** @test */
    public function ticket_quantity_must_be_at_least_one()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from("/backstage/concerts/{$concert->id}/edit")
            ->patch("/backstage/concerts/{$concert->id}/edit", $this->validParams(['ticket_quantity' => '0']))
            ->assertStatus(302);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('ticket_quantity');
        tap($concert->fresh(), function($concert){
            $this->assertEquals(5, $concert->ticket_quantity);
        });
    }
}
