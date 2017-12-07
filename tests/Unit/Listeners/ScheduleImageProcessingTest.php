<?php

namespace Tests\Unit\Listeners;

use App\Image;
use Tests\TestCase;
use App\Jobs\ProcessImage;
use Illuminate\Support\Facades\Queue;
use App\Events\ImageUploaded;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleImageProcessingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_queues_a_job_to_process_an_image()
    {
        Queue::fake();

        $image = create(Image::class);

        ImageUploaded::dispatch($image);

        $this->assertJob(ProcessImage::class, compact('image'));

    }
}