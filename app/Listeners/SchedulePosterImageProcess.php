<?php

namespace App\Listeners;

use App\Jobs\ProcessPosterImage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SchedulePosterImageProcess
{
    public function handle($event)
    {
        if($event->concert->hasPoster()){
            ProcessPosterImage::dispatch($event->concert);
        }
    }
}
