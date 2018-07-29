<?php

namespace KRLX\Notifications;

use KRLX\Show;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use KRLX\Mail\ShowSubmitted as ShowSubmittedMailTemplate;

class ShowSubmitted extends Notification
{
    use Queueable;

    public $show;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Show $show)
    {
        $this->show = $show;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new ShowSubmittedMailTemplate($this->show))
                    ->to($notifiable->email)
                    ->subject("{$this->show->title} Application Submitted");
    }
}
