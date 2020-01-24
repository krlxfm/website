<?php

namespace KRLX\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use KRLX\BoardApp;

class BoardInterview extends Mailable
{
    use Queueable, SerializesModels;

    public $app;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(BoardApp $app)
    {
        $this->app = $app;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('manager@krlx.org')->markdown('mail.board.interview');
    }
}
