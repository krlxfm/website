<?php

namespace KRLX\Events;

use KRLX\Track;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class TrackCreating
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $track;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Track $track)
    {
        $this->track = $track;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
