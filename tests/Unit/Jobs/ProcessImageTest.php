<?php

namespace Tests\Unit\Jobs;

use App\Image;
use Tests\TestCase;
use App\Jobs\ProcessImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_resizes_the_image_to_600_by_400()
    {
        Storage::fake('public');

        Storage::disk('public')->put(
            'images/example-image.jpg',
            file_get_contents(base_path('tests/__fixtures__/sour-worms.jpg'))
        );

        $image = create(Image::class, [
            'path' => 'images/example-image.jpg'
        ]);

        ProcessImage::dispatch($image);

        $resizedImage = Storage::disk('public')->get('images/example-image.jpg');

        list($width, $height) = getimagesizefromstring($resizedImage);
        $this->assertEquals(600, $width);
        $this->assertEquals(400, $height);
    }

    /**
     * @test
     */
    public function it_resizes_a_featured_image_to_1920_by_1080()
    {
        Storage::fake('public');

        Storage::disk('public')->put(
            'images/example-image.jpg',
            file_get_contents(base_path('tests/__fixtures__/sour-worms.jpg'))
        );

        $image = create(Image::class, [
            'path' => 'images/example-image.jpg',
            'featured_flag' => true
        ]);

        ProcessImage::dispatch($image);

        $resizedImage = Storage::disk('public')->get('images/example-image.jpg');

        list($width, $height) = getimagesizefromstring($resizedImage);
        $this->assertEquals(1920, $width);
        $this->assertEquals(1080, $height);
    }

    /** @test */
    public function it_optimizes_the_image()
    {
        Storage::fake('public');

        Storage::disk('public')->put(
            'images/sour-worms-resized.jpg',
            file_get_contents(base_path('tests/__fixtures__/sour-worms-resized.jpg'))
        );

        $image = create(Image::class, [
            'path' => 'images/sour-worms-resized.jpg'
        ]);

        ProcessImage::dispatch($image);

        $optimizedImageSize = Storage::disk('public')->size('images/sour-worms-resized.jpg');

        $originalSize = filesize( base_path('tests/__fixtures__/sour-worms-resized.jpg') );
        $this->assertLessThan($originalSize, $optimizedImageSize);

        $optimizedImageContents = Storage::disk('public')->get('images/sour-worms-resized.jpg');
        $controlImageContents = file_get_contents(base_path('tests/__fixtures__/sour-worms-optimized.jpg'));

        $this->assertEquals($optimizedImageContents, $controlImageContents);
    }
}