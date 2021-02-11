<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConcertAdded
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $concert;

    public function __construct($concert)
    {
        $this->concert = $concert;
    }
}
