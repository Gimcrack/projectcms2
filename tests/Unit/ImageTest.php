<?php

namespace Tests\Unit;

use App\Image;
use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_path() {
        create(Image::class, [
            'path' => null
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_unique_path()
    {
        create(Image::class, [
            'path' => 'Image Path'
        ]);

        create(Image::class, [
            'path' => 'Image Path'
        ]);
    }

    /**
     * @test
     */
    public function it_may_have_a_subject_type() {
        create(Image::class, [
            'subject_type' => null
        ]);

        $this->assertCount(1, Image::all());
    }

    /**
     * @test
     */
    public function it_may_have_a_subject_id() {
        create(Image::class, [
            'subject_id' => null
        ]);

        $this->assertCount(1, Image::all());
    }

    /** @test */
    public function it_can_have_a_product_as_a_subject()
    {
        $project = create(Project::class);

        $project->images()->save( $image = make(Image::class) );

        $this->assertInstanceOf(Project::class, $image->subject);
        $this->assertInstanceOf(Image::class, $project->images()->first() );
    }

    /** @test */
    public function it_can_be_featured()
    {
        $project = create(Project::class);

        $project->images()->save( $image = create(Image::class) );

        $this->assertFalse( $image->isFeatured() );

        $image->feature();

        $this->assertTrue( $image->isFeatured() );

        $image->feature(false);

        $this->assertFalse($image->isFeatured());
    }
}