<?php

namespace KRLX\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use KRLX\Show;

class InitialTimeAssigned extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The show instance.
     *
     * @var Show
     */
    public $show;
    public $schedule_lock;
    public $first_show;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Show $show)
    {
        $this->show = $show;
        $this->subject("{$show->title} Time Assigned");
        $this->from('scheduling@krlx.org');
        $this->schedule_lock = $show->term->on_air->copy()->subDay();

        // Compute the date of the first episode.
        $first = $this->schedule_lock->copy()->modify('next '.$show->day)->setTimeFromTimeString($show->start);
        if ($first < $show->term->on_air) {
            $first->addWeek();
        }
        $this->first_show = $first;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.shows.initial');
    }
}
