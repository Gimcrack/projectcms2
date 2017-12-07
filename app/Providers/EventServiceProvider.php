<?php

namespace App\Providers;

use App\Events\ImageUploaded;
use App\Listeners\NotifyApprovers;
use App\Events\ProjectReadyForApproval;
use App\Listeners\ScheduleImageProcessing;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ImageUploaded::class => [
            ScheduleImageProcessing::class,
        ],

        ProjectReadyForApproval::class => [
            NotifyApprovers::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
