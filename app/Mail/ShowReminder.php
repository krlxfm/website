<?php

namespace KRLX\Mail;

use KRLX\Show;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShowReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The show instance.
     *
     * @var Show
     */
    public $show;
    public $deadline_diff;

    /**
     * Create a new message instance.
     *
     * @param  Show  $show
     * @return void
     */
    public function __construct(Show $show)
    {
        $this->show = $show;
        $this->subject("{$show->title} Application Reminder");
        $this->from('scheduling@krlx.org');

        $now = Carbon::now();
        $this->deadline_diff = $now->diff($show->term->applications_close);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.shows.remind');
    }
}
