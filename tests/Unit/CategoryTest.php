<?php

namespace Tests\Unit;

use App\Category;
use App\Events\CategoryWasCreated;
use App\Events\CategoryWasDestroyed;
use App\Events\CategoryWasUpdated;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_name()
    {
        create(Category::class, [
            'name' => null
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_unique_name()
    {
        create(Category::class, [
            'name' => 'Category Name'
        ]);

        create(Category::class, [
            'name' => 'Category Name'
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_description()
    {
        create(Category::class, [
            'description' => null
        ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_category_is_created()
    {
        Event::fake();
        $category = create(Category::class);
        $this->assertEvent(CategoryWasCreated::class, [ 'category' => $category ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_category_is_updated()
    {
        Event::fake();
        // given a published category
        $category = create(Category::class);

        // act - update the category
        $category->update([
            'name' => 'New Name'
        ]);

        $this->assertEvent(CategoryWasUpdated::class, [ 'category' => $category ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_category_is_destroyed()
    {
        Event::fake();
        // given a category
        $category = create(Category::class);

        // act - delete the category
        $category->delete();
        $this->assertEvent(CategoryWasDestroyed::class, [ 'category' => $category ]);
    }
 }
