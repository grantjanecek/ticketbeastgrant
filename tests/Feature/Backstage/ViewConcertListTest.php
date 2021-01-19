<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use App\Models\User;
use App\Models\Concert;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertListTest extends TestCase
{
    use RefreshDatabase;

   /** @test */
   public function guests_cannot_view_a_promoters_concert_list()
   {
        $this->get('/backstage/concerts')->assertRedirect('/login');
   }

    /** @test */
   public function promoters_can_only_view_their_own_concerts()
   {
       $user = User::factory()->create();
       $otherUser = User::factory()->create();
       $publishedConcertA = Concert::factory()->published()->create(['user_id' => $user->id]);
       $publishedConcertB = Concert::factory()->published()->create(['user_id' => $otherUser->id]);
       $publishedConcertC = Concert::factory()->published()->create(['user_id' => $user->id]);

       $unpublishedConcertA = Concert::factory()->unpublished()->create(['user_id' => $user->id]);
       $unpublishedConcertB = Concert::factory()->unpublished()->create(['user_id' => $otherUser->id]);
       $unpublishedConcertC = Concert::factory()->unpublished()->create(['user_id' => $user->id]);

       $response = $this->actingAs($user)->get('/backstage/concerts');

       $response->assertSuccessful();

       $response->viewData('publishedConcerts')->assertEquals([
           $publishedConcertA,
           $publishedConcertC
       ]);

       $response->viewData('unpublishedConcerts')->assertEquals([
           $unpublishedConcertA,
           $unpublishedConcertC
       ]);
   }
}
