<?php

namespace KRLX\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShowReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The show instance.
     *
     * @var Show
     */
    public $show;

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
