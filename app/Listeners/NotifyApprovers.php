<?php

namespace App\Listeners;

use App\User;
use Illuminate\Support\Facades\Log;
use App\Events\ProjectReadyForApproval;
use App\Notifications\ProjectNeedsApproval;
use Illuminate\Support\Facades\Notification;

class NotifyApprovers
{
    /**
     * Handle the event.
     *
     * @param  $event
     * @return void
     */
    public function handle(ProjectReadyForApproval $event)
    {
        if ( ! $this->ready($event) ) {
            Log::info("Ignoring ProjectReadyForApproval Event For Non-Ready Project {$event->project->name}");
            return;
        }

        Notification::send(
            User::approvers()->get(),
            new ProjectNeedsApproval($event->project)
        );
    }

    /**
     * @param ProjectReadyForApproval $event
     * @return bool
     */
    public function ready(ProjectReadyForApproval $event): bool
    {
        return $event->project->ready() && $event->project->unapproved();
    }
}
