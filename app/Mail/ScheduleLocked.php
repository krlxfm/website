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
        $this->subject("[KRLX] {$this->show->title} - Final Time and Policy Reminders");
        return $this->markdown('mail.shows.final');
    }
}
