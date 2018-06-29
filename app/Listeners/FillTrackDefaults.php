<?php

namespace KRLX\Listeners;

use KRLX\Events\TrackCreating;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FillTrackDefaults
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TrackCreating  $event
     * @return void
     */
    public function handle(TrackCreating $event)
    {
        $track = $event->track;

        $track->content = $track->content ?? [];
        $track->scheduling = $track->scheduling ?? [];
        $track->etc = $track->etc ?? [];
    }
}
