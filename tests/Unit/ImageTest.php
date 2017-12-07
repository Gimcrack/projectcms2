<?php

namespace Tests\Unit;

use App\Events\ImageWasCreated;
use App\Events\ImageWasDestroyed;
use App\Events\ImageWasUpdated;
use App\Image;
use App\Project;
use Illuminate\Support\Facades\Event;
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

    /** @test */
    function an_event_is_dispatched_when_a_image_is_created()
    {
        Event::fake();
        $image = create(Image::class);
        $this->assertEvent(ImageWasCreated::class, [ 'image' => $image ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_image_is_updated()
    {
        Event::fake();
        // given a published image
        $image = create(Image::class);

        // act - update the image
        $image->update([
            'path' => 'New Path'
        ]);

        $this->assertEvent(ImageWasUpdated::class, [ 'image' => $image ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_image_is_destroyed()
    {
        Event::fake();
        // given a image
        $image = create(Image::class);

        // act - delete the image
        $image->delete();
        $this->assertEvent(ImageWasDestroyed::class, [ 'image' => $image ]);
    }
}