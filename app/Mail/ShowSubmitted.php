<?php

namespace KRLX\Mail;

use KRLX\Show;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShowSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $show;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Show $show)
    {
        $this->show = $show;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.shows.submitted')
                    ->with(['show' => $this->show]);
    }
}
