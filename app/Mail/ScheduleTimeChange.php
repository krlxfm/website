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
        return $this->markdown('mail.shows.update');
    }
}
