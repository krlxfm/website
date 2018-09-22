<?php

namespace KRLX\Listeners;

use KRLX\Events\TermCreating;

class FillTermDefaults
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
     * @param  TermCreating  $event
     * @return void
     */
    public function handle(TermCreating $event)
    {
        $term = $event->term;

        $term->track_managers = $term->track_managers ?? [];
    }
}
