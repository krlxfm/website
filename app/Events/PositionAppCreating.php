<?php

namespace KRLX\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use KRLX\PositionApp;

class PositionAppCreating
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $position_app;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PositionApp $position_app)
    {
        $this->position_app = $position_app;
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
