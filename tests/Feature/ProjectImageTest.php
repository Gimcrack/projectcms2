<?php

namespace Tests\Feature;

use App\Image;
use App\Project;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_get_a_listing_of_the_resource()
    {
        $images = create(Image::class, 3);

        $project = create(Project::class);
        $project->images()->saveMany($images);

        $other_image = create(Image::class);

        $this->actingAsAdmin()
            ->api()
            ->get(["projects.images.index",$project])
            ->response()
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonModelCollection( $images )
            ->assertJsonMissingModel($other_image);

    }

    /**
     * @test
     */
    public function it_can_get_a_single_resource()
    {
        $project = create(Project::class);

        $project->images()->save($image = create(Image::class));

        $this->actingAsAdmin()
            ->api()
            ->get(["projects.images.show",$project,$image])
            ->response()
            ->assertStatus(200)
            ->assertJsonModel( $image );
    }

    /** @test */
    public function it_can_add_an_image_to_a_project()
    {
        $project = create(Project::class);

        Storage::fake('public');

        $image = File::image('category-image.png', $width = 600, $height = 400);

        $atts = compact('image');

        $this->actingAsAdmin()
            ->api()
            ->post(["projects.images.store", $project], $atts)
            ->response()
            ->assertStatus(201);

        tap( $project->images()->first(), function($image_model) use ($image) {

            $this->assertNotNull($image_model->path);

            Storage::disk('public')->assertExists( $image_model->path );
        });
    }

    /** @test */
    public function it_can_update_an_image()
    {
        $project = create(Project::class);

        $project->images()->save($original = create(Image::class));

        $image = File::image('project-image.png', $width = 600, $height = 400);

        Storage::fake('public');

        $atts = compact('image');

        $this->actingAsAdmin()
            ->api()
            ->patch(["projects.images.update", $project, $original], $atts)
            ->response()
            ->assertStatus(202);

        tap( $original->fresh(), function($image_model) use ($image) {

            $this->assertNotNull($image_model->path);

            Storage::disk('public')->assertExists( $image_model->path );
        });
    }

    /** @test */
    public function it_can_delete_an_image()
    {
        Storage::fake('public');

        $project = create(Project::class);

        $project->images()->save($image = create(Image::class));

        $path = $image->path;

        Storage::disk('public')->assertExists($path);

        $this->actingAsAdmin()
            ->api()
            ->delete(["projects.images.destroy", $project, $image])
            ->response()
            ->assertStatus(202);

        $this->assertCount(0, $project->images );

        Storage::disk('public')->assertMissing($path);
    }
}