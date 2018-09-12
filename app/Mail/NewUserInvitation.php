<?php

namespace KRLX\Mail;

use KRLX\Show;
use KRLX\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $show;
    public $sender;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Show $show, User $sender)
    {
        $this->show = $show;
        $this->sender = $sender;
        $this->subject("Invitation to join {$show->title}");
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.shows.new-invite');
    }
}
