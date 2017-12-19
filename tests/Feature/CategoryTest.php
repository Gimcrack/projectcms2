<?php

namespace Tests\Feature;

use App\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_get_a_listing_of_the_resource()
    {
        $categories = create(Category::class, 3);

        $this->actingAsUser()
            ->api()
            ->get("categories.index")
            ->response()
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonModelCollection($categories);
    }

    /** @test */
    public function it_can_get_a_single_category()
    {
        $category = create(Category::class);

        $this->actingAsUser()
            ->api()
            ->get(["categories.show",$category])
            ->response()
            ->assertStatus(200)
            ->assertJsonModel($category);
    }

    /** @test */
    public function it_cannot_create_a_category_without_a_name()
    {
        $atts = make_array(Category::class, [
            'name' => null
        ]);

        $this->actingAsUser()
            ->api()
            ->post("categories.store", $atts)
            ->response()
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function it_cannot_create_a_category_with_a_duplicate_name()
    {
        create(Category::class, ['name' => 'Category Name']);

        $atts = make_array(Category::class, [
            'name' => 'Category Name'
        ]);

        $this->actingAsUser()
            ->api()
            ->post("categories.store", $atts)
            ->response()
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function it_cannot_create_a_category_without_a_description()
    {
        $atts = make_array(Category::class, [
            'description' => null
        ]);

        $this->actingAsUser()
            ->api()
            ->post("categories.store", $atts)
            ->response()
            ->assertStatus(422)
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function it_can_create_a_category_with_valid_attributes()
    {
        $atts = make_array(Category::class);

        $this->actingAsUser()
            ->api()
            ->post("categories.store", $atts)
            ->response()
            ->assertStatus(201);

        $this->assertDatabaseHas('categories', $atts);
    }

    /** @test */
    public function it_can_be_updated()
    {
        $category = create(Category::class);

        $this->actingAsUser()
            ->api()
            ->patch(["categories.update", $category],[
                'name' => 'New Name'
            ])
            ->response()
            ->assertStatus(202);

        $this->assertDatabaseHas('categories',[
            'name' => 'New Name'
        ]);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $category = create(Category::class);

        $this->actingAsUser()
            ->delete(["categories.destroy", $category])
            ->response()
            ->assertStatus(202);

        $this->assertDatabaseMissing('categories', $category->toArray());
    }
 }
