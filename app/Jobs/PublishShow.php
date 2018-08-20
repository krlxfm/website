<?php

namespace KRLX\Jobs;

use KRLX\Show;
use Illuminate\Bus\Queueable;
use Google_Service_Calendar_Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PublishShow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $show;
    public $calendar;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Show $show)
    {
        $this->calendar = resolve('Google_Service_Calendar');
        $this->show = $show;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!$this->show->gc_show_id) {
            $this->publishNewShow();
        } else if (!$this->show->day or !$this->show->start or !$this->show->end) {
            $this->removeShow();
        } else {
            $this->updateShow();
        }
    }

    /**
     * Publish the show to the calendar for the first time.
     *
     * @return void
     */
    private function publishNewShow()
    {
        $start = $this->show->term->on_air->copy()
                                          ->setTimeFromTimeString($this->show->start)
                                          ->subDay()
                                          ->modify('next '.$this->show->day);
        if($start <= $this->show->term->on_air) {
            $start->addWeek();
        }
        $end = $start->copy()->setTimeFromTimeString($this->show->end);
        if($end <= $start) {
            $end->addDay();
        }
        $event = new Google_Service_Calendar_Event([
            'summary' => $this->show->title,
            'description' => implode(', ', $this->show->hosts->pluck('full_name')->all()),
            'start' => ['dateTime' => $start->toRfc3339String()],
            'end' => ['dateTime' => $end->toRfc3339String()],
        ]);

        $result = $this->calendar->events->insert('primary', $event);
        dump($result);
    }

    /**
     * Removes an event from the calendar.
     *
     * @return void
     */
    private function removeShow()
    {
        $this->show->published_day = null;
        $this->show->published_start = null;
        $this->show->published_end = null;
        $this->show->save();
    }
}
