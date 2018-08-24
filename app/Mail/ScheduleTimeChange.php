<?php

namespace KRLX\Mail;

use KRLX\Show;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use KRLX\Mail\InitialTimeAssigned;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScheduleTimeChange extends InitialTimeAssigned
{
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.shows.update');
    }
}
