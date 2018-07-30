<?php

namespace KRLX\Notifications;

use KRLX\Show;
use KRLX\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ShowInvitation extends Notification
{
    use Queueable;

    public $show;
    public $sender;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Show $show, User $sender)
    {
        $this->show = $show;
        $this->sender = $sender;
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
        return (new MailMessage)->markdown('mail.shows.invitation', [
                    'show' => $this->show,
                    'sender' => $this->sender,
                    'recipient' => $notifiable,
                ])
                ->subject('Invitation to join '.$this->show->title);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
