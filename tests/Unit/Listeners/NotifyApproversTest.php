<?php

namespace Tests\Unit;

use App\User;
use App\Project;
use Tests\TestCase;
use App\Events\ProjectReadyForApproval;
use App\Notifications\ProjectNeedsApproval;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotifyApproversTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_sends_a_notification_to_approvers_when_a_project_is_ready_for_approval()
    {
        // given an approver
        $approver = create_state(User::class, 'approver');

        // and a standard user
        $user = create(User::class);

        // create a ready project
        $project = create_state(Project::class,'ready');

        // dispatch the event
        ProjectReadyForApproval::dispatch($project);

        // assert that a notification was sent to the approver
        $this->assertNotification(
            ProjectNeedsApproval::class,
            $approver,
            compact('project')
        );

        // assert that a notification was not sent to the user
        $this->assertNotificationNotSent(
            ProjectNeedsApproval::class,
            $user,
            compact('project')
        );
    }

    /**
     * @test
     */
    public function it_does_not_send_a_notification_to_approvers_when_a_project_is_created_or_updated()
    {
        // given an approver
        $approver = create_state(User::class, 'approver');

        // create a project
        $project = create(Project::class);

        // assert that a notification was not sent
        $this->assertNotificationNotSent(
            ProjectNeedsApproval::class,
            $approver,
            compact('project')
        );

        // update the project
        $project->update([
            'name' => 'New Name'
        ]);

        // assert that a notification was not sent
        $this->assertNotificationNotSent(
            ProjectNeedsApproval::class,
            $approver,
            compact('project')
        );
    }
 }
