<?php

namespace App\Listeners;

use App\Jobs\ProcessImage;
use App\Events\ImageUploaded;

class ScheduleImageProcessing
{
    /**
     * Handle the event.
     *
     * @param ImageUploaded $event
     * @return void
     */
    public function handle(ImageUploaded $event)
    {
        ProcessImage::dispatch($event->image);
    }
}
