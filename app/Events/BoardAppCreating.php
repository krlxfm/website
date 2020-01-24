<?php

namespace KRLX\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use KRLX\BoardApp;

class BoardAppCreating
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $board_app;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BoardApp $board_app)
    {
        $this->board_app = $board_app;
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
