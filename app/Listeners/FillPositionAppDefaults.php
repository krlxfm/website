<?php

namespace KRLX\Listeners;

use KRLX\Events\PositionAppCreating;

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
