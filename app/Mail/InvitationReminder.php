<?php

namespace KRLX\Mail;

use KRLX\User;
use KRLX\Show;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvitationReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Show $show, User $user)
    {
        $this->show = $show;
        $this->user = $user;
        $this->subject("{$show->title} Invitation Reminder");
        $this->from('scheduling@krlx.org');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.shows.invite-remind');
    }
}
