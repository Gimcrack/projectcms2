<?php

namespace Tests\Feature;

use App\Image;
use Tests\TestCase;
use App\Events\ImageUploaded;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_image_is_uploaded_if_it_is_included()
    {
        Queue::fake();

        Storage::fake('public');

        $image = File::image('product-image.png', $width = 600, $height = 400);

        $atts = compact('image');

        $this->actingAsUser()
            ->api()
            ->post("images.store", $atts)
            ->response()
            ->assertStatus(201);

        tap( Image::first(), function($image_model) use ($image) {

            $this->assertNotNull($image_model->path);

            Storage::disk('public')->assertExists( $image_model->path );

            $this->assertFileEquals(
                $image->getPathname(),
                Storage::disk('public')->path( $image_model->path )
            );
        });
    }

    /** @test */
    public function image_must_be_an_image()
    {
        Storage::fake('public');

        $image = File::create('not-an-image.pdf');

        $atts = compact('image');

        $this->actingAsUser()
            ->api()
            ->post("images.store", $atts)
            ->response()
            ->assertStatus(422)
            ->assertJsonValidationErrors('image');
    }

    /** @test */
    public function image_must_be_at_least_600px_wide()
    {
        Storage::fake('public');

        $image = File::image('an-image.png',$width = 599, $height = 400);

        $atts = compact('image');

        $this->actingAsUser()
            ->api()
            ->post("images.store", $atts)
            ->response()
            ->assertStatus(422)
            ->assertJsonValidationErrors('image');
    }

    /** @test */
    public function image_must_be_at_least_400px_tall()
    {
        Storage::fake('public');

        $image = File::image('an-image.png',$width = 600, $height = 399);

        $atts = compact('image');

        $this->actingAsUser()
            ->api()
            ->post("images.store", $atts)
            ->response()
            ->assertStatus(422)
            ->assertJsonValidationErrors('image');
    }

    /** @test */
    public function it_can_get_an_index_of_images()
    {
        $images = create(Image::class,3);

        $this->actingAsUser()
            ->api()
            ->get("images.index")
            ->response()
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonModelCollection($images);
    }

    /** @test */
    public function it_can_get_an_image()
    {
        $image = create(Image::class);

        $this
            ->actingAsUser()
            ->api()
            ->get(["images.show",$image])
            ->response()
                ->assertStatus(200)
                ->assertJsonModel($image);
    }

    /** @test */
    public function it_can_update_an_image()
    {
        Queue::fake();

        $original = create(Image::class);

        Storage::fake('public');

        $image = File::image('product-image.png', $width = 600, $height = 400);

        $atts = compact('image');

        $this->actingAsUser()
            ->api()
            ->patch(["images.update", $original], $atts)
                ->response()
                ->assertStatus(202);

        tap( $original->fresh(), function($image_model) use ($image) {

            $this->assertNotNull($image_model->path);

            Storage::disk('public')->assertExists( $image_model->path );

            $this->assertFileEquals(
                $image->getPathname(),
                Storage::disk('public')->path( $image_model->path )
            );
        });
    }

    /** @test */
    public function it_can_delete_an_image()
    {
        Storage::fake('public');

        $image = create(Image::class);

        $path = $image->path;

        Storage::disk('public')->assertExists($path);

        $this->actingAsUser()
            ->api()
            ->delete(["images.destroy",$image])
            ->response()
            ->assertStatus(202);

        $this->assertCount(0, Image::all());

        Storage::disk('public')->assertMissing($path);

    }

    /** @test */
    public function it_fires_an_event_when_an_image_is_uploaded()
    {
        $this->fakeEvent( ImageUploaded::class );

        $image = $this->uploadImage();

        $this->assertEvent(ImageUploaded::class, ['image' => $image ] );
    }

    /** @test */
    public function it_fires_an_event_when_an_image_is_updated()
    {
        $original = create(Image::class);

        $this->fakeEvent( ImageUploaded::class );

        Storage::fake('public');

        $image = File::image('product-image.png', $width = 600, $height = 400);

        $atts = compact('image');

        $this->actingAsUser()
            ->api()
            ->patch(["images.update",$original], $atts);

        $this->assertEvent(ImageUploaded::class, ['image' => $original->fresh() ] );
    }


    private function uploadImage($width = 600, $height = 400)
    {
        Storage::fake('public');

        $image = File::image('product-image.png', $width, $height);

        $atts = compact('image');

        $this->actingAsUser()
            ->api()
            ->post("images", $atts);

        return Image::first();
    }


}