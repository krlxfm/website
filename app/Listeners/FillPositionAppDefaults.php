<?php

namespace KRLX\Listeners;

use KRLX\Events\PositionAppCreating;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FillPositionAppDefaults
{
    /**
     * Handle the event.
     *
     * @param  PositionAppCreating  $event
     * @return void
     */
    public function handle(PositionAppCreating $event)
    {
        $position_app = $event->position_app;
        $position_app->responses = [];
    }
}
