<?php

namespace KRLX\Listeners;

use KRLX\Events\BoardAppCreating;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FillBoardAppDefaults
{
    /**
     * Handle the event.
     *
     * @param  BoardAppCreating  $event
     * @return void
     */
    public function handle(BoardAppCreating $event)
    {
        $board_app = $event->board_app;

        $board_app->year = date('Y');
        $board_app->interview_schedule = [];
        $board_app->common = [];
    }
}
