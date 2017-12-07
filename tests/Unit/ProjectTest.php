<?php

namespace Tests\Unit;

use App\Events\ProjectReadyForApproval;
use App\Events\ProjectWasCreated;
use App\Events\ProjectWasDestroyed;
use App\Events\ProjectWasUpdated;
use App\Project;
use App\User;
use function create_state;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_name()
    {
        create(Project::class, [
            'name' => null
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_unique_name()
    {
        create(Project::class, [
            'name' => 'Project Name'
        ]);

        create(Project::class, [
            'name' => 'Project Name'
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_description()
    {
        create(Project::class, [
            'description' => null
        ]);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_have_a_category()
    {
        create(Project::class,[
            'category_id' => null
        ]);
    }

    /** @test */
    public function it_may_have_a_cost()
    {
        $project = create(Project::class, [
            'cost' => null
        ]);

        $this->assertTrue( Project::first()->is($project) );
    }

    /** @test */
    public function it_may_have_a_start_date()
    {
        $project = create(Project::class, [
            'starts_at' => null
        ]);

        $this->assertTrue( Project::first()->is($project) );
    }

    /** @test */
    public function it_may_have_an_end_date()
    {
        $project = create(Project::class, [
            'ends_at' => null
        ]);

        $this->assertTrue( Project::first()->is($project) );
    }

    /** @test */
    public function it_is_not_approved_by_default()
    {
        $project = create(Project::class);

        $this->assertFalse( $project->approved() );
    }

    /** @test */
    public function it_can_be_approved_by_an_approver()
    {
        $approver = create_state(User::class,'approver');

        $project = create(Project::class, [
            'approved_flag' => false
        ]);

        $project->approveBy($approver);

        $this->assertTrue( $project->approved() );
        $this->assertTrue( $project->approver->is($approver) );
    }

    /** @test */
    public function it_can_be_unapproved_by_an_approver()
    {
        $project = create_state(Project::class, 'approved');

        $this->assertTrue( $project->approved() );

        $project->unapprove();

        $this->assertFalse( $project->approved() );
        $this->assertNull( $project->approved_by );
    }
    
    /**
     * @test
     * @expectedException \Illuminate\Auth\AuthenticationException
     */
    public function it_cannot_be_approved_by_a_non_approver()
    {
        $user = create(User::class);

        $project = create(Project::class, [
            'approved_flag' => false
        ]);

        $project->approveBy($user);

        $this->assertFalse( $project->approved() );
        $this->assertFalse( $project->approver->is($user) );
    }

    /** @test */
    public function it_is_not_published_by_default()
    {
        $project = create(Project::class);

        $this->assertFalse( $project->published() );
        $this->assertNull( $project->published_at );
    }

    /** @test */
    public function it_can_be_published_in_the_past()
    {
        $project = create(Project::class);

        $project->publish("1900-01-01 00:00:00");

        $this->assertTrue( $project->published() );
    }

    /** @test */
    public function it_can_be_published_now()
    {
        $project = create(Project::class);

        $project->publish();

        $this->assertTrue( $project->published() );
    }

    /** @test */
    public function it_can_be_published_in_the_future()
    {
        $project = create(Project::class);

        $project->publish("2900-01-01 00:00:00");

        $this->assertFalse( $project->published() );
    }

    /** @test */
    public function it_can_stop_publishing_in_the_past()
    {
        $project = create_state(Project::class,'published');

        $project->unpublish("2010-01-01 00:00:00");

        $this->assertFalse($project->published());
    }

    /** @test */
    public function it_can_stop_publishing_now()
    {
        $project = create_state(Project::class,'published');

        $project->unpublish();

        $this->assertFalse($project->published());
    }

    /** @test */
    public function it_can_stop_publishing_in_the_future()
    {
        $project = create_state(Project::class,'published');

        $project->unpublish("2100-01-01 00:00:00");

        $this->assertTrue($project->published());
    }

    /** @test */
    public function it_is_not_ready_for_approval_by_default()
    {
        $project = create(Project::class);

        $this->assertFalse($project->ready_flag);
    }

    /** @test */
    public function it_can_be_readied_for_approval()
    {
        $project = create(Project::class);

        $this->assertFalse($project->ready_flag);

        $project->ready(true);

        $this->assertTrue( $project->fresh()->ready() );
    }

    /** @test */
    public function the_project_is_not_ready_after_an_update()
    {
        $project = create_state(Project::class,'ready');

        $project->update([
            'name' => 'New Name'
        ]);

        $this->assertFalse( $project->fresh()->ready() );
    }


    /** @test */
    function an_event_is_dispatched_when_a_project_is_created()
    {
        Event::fake();
        $project = create(Project::class);
        $this->assertEvent(ProjectWasCreated::class, [ 'project' => $project ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_project_is_updated()
    {
        Event::fake();
        // given a published project
        $project = create(Project::class);

        // act - update the project
        $project->update([
            'name' => 'New Name'
        ]);

        $this->assertEvent(ProjectWasUpdated::class, [ 'project' => $project ]);
    }

    /** @test */
    function an_event_is_dispatched_when_a_project_is_destroyed()
    {
        Event::fake();
        // given a project
        $project = create(Project::class);

        // act - delete the project
        $project->delete();
        $this->assertEvent(ProjectWasDestroyed::class, [ 'project' => $project ]);
    }

    /** @test */
    public function an_event_is_dispatched_when_a_project_is_ready_for_approval()
    {
        Event::fake();

        // given a project
        $project = create(Project::class);

        // act - ready the project
        $project->ready(true);
        $this->assertEvent(ProjectReadyForApproval::class, [ 'project' => $project ]);
    }


}
