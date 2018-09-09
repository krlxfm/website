<?php

namespace KRLX\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class ScheduleLocked extends InitialTimeAssigned
{
    use Queueable, SerializesModels;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.shows.final');
    }
}
