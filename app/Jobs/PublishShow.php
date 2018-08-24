<?php

namespace KRLX\Jobs;

use KRLX\Show;
use Illuminate\Bus\Queueable;
use KRLX\Mail\ScheduleTimeChange;
use KRLX\Mail\InitialTimeAssigned;
use Google_Service_Calendar_Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Google_Service_Calendar_EventDateTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PublishShow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $show;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Show $show)
    {
        $this->show = $show;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->show->gc_show_id) {
            $this->publishNewShow();
        } elseif (! $this->show->day or ! $this->show->start or ! $this->show->end) {
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
        $calendar = resolve('Google_Service_Calendar');
        $event = new Google_Service_Calendar_Event([
            'summary' => $this->show->title,
            'description' => implode(', ', $this->show->hosts->pluck('full_name')->all()),
        ]);

        $this->setEventTimeDetails($event);

        $this->show->gc_show_id = $calendar->events->insert('primary', $event)->id;
        Mail::to($this->show->hosts)->queue(new InitialTimeAssigned($this->show));
        $this->syncShowTimes();
    }

    /**
     * Sets a Google Calendar Event's start, end, and recurrence based on the
     * show being saved.
     *
     * @return void
     */
    private function setEventTimeDetails($event)
    {
        $start = $this->show->term->on_air->copy()
                                          ->subDay()
                                          ->modify('next '.$this->show->day)
                                          ->setTimeFromTimeString($this->show->start);
        if ($start <= $this->show->term->on_air) {
            $start->addWeek();
        }
        $end = $start->copy()->setTimeFromTimeString($this->show->end);
        if ($end <= $start) {
            $end->addDay();
        }
        $recurrence_end = $this->show->term->off_air->copy()->addDay();
        if ($recurrence_end->copy()->setTimeFromTimeString($this->show->start) >= $recurrence_end) {
            $recurrence_end->subDay();
        }

        $event_start = new Google_Service_Calendar_EventDateTime;
        $event_start->setDateTime($start->toRfc3339String());
        $event_start->setTimeZone(config('app.timezone'));
        $event->setStart($event_start);

        $event_end = new Google_Service_Calendar_EventDateTime;
        $event_end->setDateTime($end->toRfc3339String());
        $event_end->setTimeZone(config('app.timezone'));
        $event->setEnd($event_end);

        $event->setRecurrence(['RRULE:FREQ=WEEKLY;UNTIL='.$recurrence_end->format('Ymd\THis\Z')]);
    }

    /**
     * Removes an event from the calendar.
     *
     * @return void
     */
    private function removeShow()
    {
        $calendar = resolve('Google_Service_Calendar');
        $calendar->events->delete('primary', $this->show->gc_show_id);
        $this->show->gc_show_id = null;
        $this->syncShowTimes();
    }

    /**
     * Updates a calendar event with new details.
     *
     * @return void
     */
    private function updateShow()
    {
        $calendar = resolve('Google_Service_Calendar');
        $event = $calendar->events->get('primary', $this->show->gc_show_id);
        $this->setEventTimeDetails($event);
        $calendar->events->update('primary', $this->show->gc_show_id, $event);
        Mail::to($this->show->hosts)->queue(new ScheduleTimeChange($this->show));
        $this->syncShowTimes();
    }

    /**
     * Sync a show's "published" times to its "working" times.
     *
     * @return void
     */
    private function syncShowTimes()
    {
        $this->show->published_day = $this->show->day;
        $this->show->published_start = $this->show->start;
        $this->show->published_end = $this->show->end;
        $this->show->save();
    }
}
