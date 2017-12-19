<?php

namespace Tests\Feature;

use App\Project;
use App\User;
use function create_state;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_get_a_listing_of_the_resource()
    {
        $projects = create(Project::class, 3);

        $this->actingAsUser()
            ->api()
            ->get("projects.index")
            ->response()
                ->assertStatus(200)
                ->assertJsonCount(3)
                ->assertJsonModelCollection($projects);
    }

    /** @test */
    public function it_can_get_a_single_project()
    {
        $project = create(Project::class);

        $this->actingAsUser()
            ->api()
            ->get(["projects.show",$project])
            ->response()
            ->assertStatus(200)
                ->assertJsonModel($project);
    }

    /** @test */
    public function it_cannot_create_a_project_without_a_name()
    {
        $atts = make_array(Project::class, [
            'name' => null
        ]);

        $this->actingAsUser()
             ->api()
             ->post("projects.store", $atts)
             ->response()
             ->assertStatus(422)
             ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function it_cannot_create_a_project_with_a_duplicate_name()
    {
        create(Project::class, ['name' => 'Project Name']);

        $atts = make_array(Project::class, [
            'name' => 'Project Name'
        ]);

        $this->actingAsUser()
            ->api()
            ->post("projects.store", $atts)
            ->response()
                ->assertStatus(422)
                ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function it_cannot_create_a_project_without_a_description()
    {
        $atts = make_array(Project::class, [
            'description' => null
        ]);

        $this->actingAsUser()
            ->api()
            ->post("projects.store", $atts)
            ->response()
            ->assertStatus(422)
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function it_can_create_a_project_with_valid_attributes()
    {
        $atts = make_array(Project::class);

        $this->actingAsUser()
            ->api()
            ->post("projects.store", $atts)
            ->response()
            ->assertStatus(201);

        $this->assertDatabaseHas('projects', $atts);
    }

    /** @test */
    public function it_can_be_readied_for_approval()
    {
        $project = create(Project::class);

        $this->assertFalse($project->ready());

        $this->actingAsUser()
            ->api()
            ->post(["projects.ready.store", $project])
            ->response()
            ->assertStatus(201);

        $this->assertTrue($project->fresh()->ready());
    }

    /** @test */
    public function it_can_be_approved_by_an_approver()
    {
        $approver = create_state(User::class, 'approver');
        $project = create(Project::class);

        $this->assertFalse( $project->approved() );

        $this->actingAs($approver)
            ->api()
            ->post(["projects.approval.store",$project])
            ->response()
                ->assertStatus(201);

        $this->assertTrue( $project->fresh()->approved() );
    }

    /** @test */
    public function it_can_be_unapproved_by_an_approver()
    {
        $approver = create_state(User::class, 'approver');
        $project = create_state(Project::class,'approved');

        $this->assertTrue( $project->approved() );

        $this->actingAs($approver)
            ->api()
            ->delete(["projects.approval.destroy", $project])
            ->response()
                ->assertStatus(202);

        $this->assertFalse( $project->fresh()->approved() );
    }

    /** @test */
    public function it_can_be_published_in_the_past()
    {
        $project = create_state(Project::class, 'unpublished');

        $this->assertFalse( $project->published() );

        $this->actingAsUser()
            ->api()
            ->post(["projects.publish.store", $project], [
                'publish_at' => '2000-01-01 00:00:00'
            ])
            ->response()
                ->assertStatus(201);

        $this->assertTrue( $project->fresh()->published() );
    }

    /** @test */
    public function it_can_be_published_now()
    {
        $project = create_state(Project::class, 'unpublished');

        $this->assertFalse( $project->published() );

        $this->actingAsUser()
            ->api()
            ->post(["projects.publish.store", $project])
            ->response()
                ->assertStatus(201);

        $this->assertTrue( $project->fresh()->published() );
    }

    /** @test */
    public function it_can_be_published_in_the_future()
    {
        $project = create_state(Project::class, 'unpublished');

        $this->assertFalse( $project->published() );

        $this->actingAsUser()
            ->api()
            ->post(["projects.publish.store", $project], [
                'published_at' => '2100-01-01 00:00:00'
            ])
            ->response()
                ->assertStatus(201);

        $this->assertFalse( $project->fresh()->published() );
        $this->assertEquals('2100-01-01 00:00:00', $project->fresh()->published_at);
    }

    /** @test */
    public function it_can_be_unpublished_in_the_past()
    {
        $project = create_state(Project::class, 'published');

        $this->assertTrue($project->published());

        $this->actingAsUser()
            ->api()
            ->delete(["projects.publish.store", $project], [
                'unpublished_at' => '2000-01-01 00:00:00'
            ])
            ->response()
                ->assertStatus(202);

        $this->assertFalse($project->fresh()->published());
        $this->assertEquals('2000-01-01 00:00:00', $project->fresh()->unpublished_at);
    }

    /** @test */
    public function it_can_be_unpublished_now()
    {
        $project = create_state(Project::class, 'published');

        $this->assertTrue($project->published());

        $this->actingAsUser()
            ->api()
            ->delete(["projects.publish.destroy", $project])
            ->response()
                ->assertStatus(202);

        $this->assertFalse($project->fresh()->published());
    }

    /** @test */
    public function it_can_be_unpublished_in_the_future()
    {
        $project = create_state(Project::class, 'published');

        $this->assertTrue($project->published());

        $this->actingAsUser()
            ->api()
            ->delete(["projects.publish.destroy", $project], [
                'unpublished_at' => '2100-01-01 00:00:00'
            ])
            ->response()
                ->assertStatus(202);

        $this->assertTrue($project->fresh()->published());
        $this->assertEquals('2100-01-01 00:00:00', $project->fresh()->unpublished_at);
    }

    /** @test */
    public function it_can_be_updated()
    {
        $project = create(Project::class);

        $this->actingAsUser()
            ->api()
            ->patch(["projects.update", $project],[
                'name' => 'New Name'
            ])
            ->response()
                ->assertStatus(202);

        $this->assertDatabaseHas('projects',[
            'name' => 'New Name'
        ]);
    }

    /** @test */
    public function it_becomes_unapproved_if_it_is_updated()
    {
        $project = create_state(Project::class, 'approved');

        $this->actingAsUser()
            ->api()
            ->patch(["projects.update", $project],[
                'name' => 'New Name'
            ])
            ->response()
            ->assertStatus(202);

        $this->assertFalse($project->fresh()->approved());
    }

    /** @test */
    public function it_becomes_unready_if_it_is_updated()
    {
        $project = create_state(Project::class, 'approved');

        $this->actingAsUser()
            ->api()
            ->patch(["projects.update", $project],[
                'name' => 'New Name'
            ])
            ->response()
            ->assertStatus(202);

        $this->assertFalse($project->fresh()->ready());
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $project = create(Project::class);

        $this->actingAsUser()
            ->delete(["projects.destroy", $project])
            ->response()
                ->assertStatus(202);

        $this->assertDatabaseMissing('projects', $project->toArray());
    }
 }
