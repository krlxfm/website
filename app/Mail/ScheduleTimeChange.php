<?php

namespace KRLX\Mail;

class ScheduleTimeChange extends InitialTimeAssigned
{
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject("TIME CHANGE for {$this->show->title}");

        return $this->markdown('mail.shows.update');
    }
}
