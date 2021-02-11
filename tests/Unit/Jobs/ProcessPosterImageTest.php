<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessPosterImage;
use App\Models\Concert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class ProcessPosterImageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_resizes_the_poster_image_to_600px_wide()
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'posters/example-poster.png',
            file_get_contents(base_path('tests/__fixtures__/full-size-poster.png')),
        );

        $concert = Concert::factory()->create([
            'poster_image_path' => 'posters/example-poster.png',
        ]);

        ProcessPosterImage::dispatch($concert);

        $resizedImage = Storage::disk('public')->get('posters/example-poster.png');
        list($width, $height) = getimagesizefromstring($resizedImage);
        $this->assertEquals(600, $width);
        $this->assertEquals(776, $height);
    }

    /** @test */
    function it_optimizes_the_poster_image()
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'posters/example-poster.png',
            file_get_contents(base_path('tests/__fixtures__/small-unoptimized-poster.png')),
        );
        $concert = Concert::factory()->create([
            'poster_image_path' => 'posters/example-poster.png',
        ]);

        ProcessPosterImage::dispatch($concert);

        $optimizedImageSize = Storage::disk('public')->size('posters/example-poster.png');
        $originalSize = filesize(base_path('tests/__fixtures__/small-unoptimized-poster.png'));
        $this->assertLessThan($originalSize, $optimizedImageSize);
    }
}
