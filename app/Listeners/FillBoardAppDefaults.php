<?php

namespace KRLX\Listeners;

use KRLX\Config;
use Carbon\Carbon;
use KRLX\Events\BoardAppCreating;

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

        $interview_options = json_decode(Config::valueOr('interview options', '[]'), true);
        $opts = [];
        foreach ($interview_options as $option) {
            $start = Carbon::parse($option['date'].' '.$option['start'].':00');
            $end = Carbon::parse($option['date'].' '.$option['end'].':00');
            $time = $start->copy();
            while ($time < $end) {
                $opts[$time->format('Y-m-d H:i:s')] = 0;
                $time->addMinutes(15);
            }
        }
        $board_app->interview_schedule = $opts;
        $common = json_decode(Config::valueOr('common questions', '[]'), true);
        $board_app->common = array_fill_keys($common, '');
    }
}
