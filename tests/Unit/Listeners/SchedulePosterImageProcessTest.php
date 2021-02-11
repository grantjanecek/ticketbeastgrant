<?php

namespace Tests\Unit\Listeners;

use Tests\TestCase;
use App\Models\Concert;
use App\Events\ConcertAdded;
use App\Jobs\ProcessPosterImage;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;


class SchedulePosterImageProcessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_job_is_queued_to_process_the_poster_image_when_a_poster_image_is_present()
    {
        Queue::fake();

        $concert = Concert::factory()->unpublished()->create([
            'poster_image_path' => 'posters/example-poster.png',
        ]);

        ConcertAdded::dispatch($concert);

        Queue::assertPushed(ProcessPosterImage::class, fn($job) => $job->concert->is($concert));
    }

    /** @test */
    function a_job_is_not_queued_when_a_poster_image_is_not_present()
    {
        Queue::fake();

        $concert = Concert::factory()->unpublished()->create([
            'poster_image_path' => null,
        ]);

        ConcertAdded::dispatch($concert);

        Queue::assertNotPushed(ProcessPosterImage::class);
    }
}
