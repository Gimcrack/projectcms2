<?php

namespace Tests\Feature;

use App\Project;
use App\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryProjectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_get_all_the_projects_of_a_category()
    {
        $category = create(Category::class);
        $projects = create(Project::class, 3);

        $category->projects()->saveMany($projects->all());

        $this->actingAsUser()
             ->get([
                "categories.projects.index",
                $category
            ])
            ->response()
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonModelCollection($projects);
    }

    /**
     * @test
     */
    public function it_can_get_a_single_resource()
    {
        $category = create(Category::class);

        $category->projects()->save( $project = create(Project::class) );

        $this->actingAsAdmin()
            ->api()
            ->get(["categories.projects.show",$category,$project])
            ->response()
            ->assertStatus(200)
            ->assertJsonModel( $project );
    }

    /** @test */
    public function it_can_add_an_image_to_a_project()
    {
        $category = create(Category::class);

        $atts = make_array(Project::class);

        $this->actingAsAdmin()
            ->api()
            ->post(["categories.projects.store", $category], $atts)
            ->response()
            ->assertStatus(201);

        $this->assertDatabaseHas('projects', [
            'category_id' => $category->id,
            'name' => $atts['name'],
            'description' => $atts['description']
        ]);
    }

    /** @test */
    public function it_can_update_a_project()
    {
        $category = create(Category::class);

        $category->projects()->save($project = create(Project::class));

        $atts = [
            'name' => 'New Name'
        ];

        $this->actingAsAdmin()
            ->api()
            ->patch(["categories.projects.update", $category, $project], $atts)
            ->response()
            ->assertStatus(202);

        $this->assertDatabaseHas('projects', [
            'category_id' => $category->id,
            'name' => 'New Name'
        ]);
    }

    /** @test */
    public function it_can_delete_a_project()
    {
        $category = create(Category::class);

        $category->projects()->save( $project = create(Project::class) );

        $this->actingAsAdmin()
            ->api()
            ->delete(["categories.projects.destroy", $category, $project])
            ->response()
            ->assertStatus(202);

        $this->assertCount(0, $category->projects );
    }
 }
