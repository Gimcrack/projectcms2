<?php

namespace Tests\Unit;

use App\Image;
use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_get_all_the_images_of_a_project()
    {
        $project = create(Project::class);

        $project->images()->save( $image = create(Image::class) );

        $retrieved = Image::ofSubject($project);

        $this->assertTrue( $retrieved->first()->is( $image ) );
    }

    /** @test */
    public function it_can_get_featured_images_of_a_project()
    {
        $project = create(Project::class);

        $project->images()->save( $image = create(Image::class) );

        $this->assertCount(0, $project->images()->featured()->get() );

        $image->feature();

        $this->assertCount(1, $project->images()->featured()->get() );
        $this->assertTrue( $project->images()->featured()->first()->is( $image ) );
    }


}