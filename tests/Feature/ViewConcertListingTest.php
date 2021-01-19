<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Concert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertListingTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    function users_can_view_a_published_concert_listing()
    {
        $concert = Concert::factory()->published()->create([
            'title' => 'The Red Cord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('December 16 2016 8:00 PM'),
            'ticket_price' => 3250,
            'venue' => 'mosh pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17324',
            'additional_information' => 'For Tickets call 9527697962',
            'published_at' => Carbon::parse('-1 week'),
        ]);

        $this->get('concerts/'.$concert->id)
            ->assertSuccessful()
            ->assertSee('The Red Cord')
            ->assertSee('with Animosity and Lethargy')
            ->assertSee('December 16, 2016')
            ->assertSee('8:00pm')
            ->assertSee('32.50')
            ->assertSee('mosh pit')
            ->assertSee('123 Example Lane')
            ->assertSee('Laraville')
            ->assertSee('ON')
            ->assertSee('17324')
            ->assertSee('For Tickets call 9527697962');
    }

    /** @test */
    function user_cannot_view_unpublished_concert_listings()
    {
        $concert = Concert::factory()->unpublished()->create();

        $this->get('concerts/'.$concert->id)
            ->assertNotFound();
    }
}
