<?php

namespace KRLX\Notifications;

use KRLX\Show;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

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
        return (new MailMessage)->markdown('mail.shows.submitted', ['show' => $this->show])
                    ->from("scheduling@krlx.org")
                    ->subject("{$this->show->title} Application Submitted");
    }
}
