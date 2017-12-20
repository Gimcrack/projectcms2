<?php

namespace Tests\Unit;

use App\Tag;
use Tests\TestCase;
use App\Events\TagWasCreated;
use App\Events\TagWasUpdated;
use App\Events\TagWasDestroyed;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_name()
    {
        create(Tag::class, [
            'name' => null
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_unique_name()
    {
        create(Tag::class, [
            'name' => 'Tag Name'
        ]);

        create(Tag::class, [
            'name' => 'Tag Name'
        ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_tag_is_created()
    {
        Event::fake();
        $tag = create(Tag::class);
        $this->assertEvent(TagWasCreated::class, [ 'tag' => $tag ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_tag_is_updated()
    {
        Event::fake();
        // given a tag
        $tag = create(Tag::class);

        // act - update the tag
        $tag->update([
            'name' => 'New Name'
        ]);
        $this->assertEvent(TagWasUpdated::class, [ 'tag' => $tag ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_tag_is_destroyed()
    {
        Event::fake();
        // given a tag
        $tag = create(Tag::class);

        // act - delete the tag
        $tag->delete();
        $this->assertEvent(TagWasDestroyed::class, [ 'tag' => $tag ]);
    }
 }
