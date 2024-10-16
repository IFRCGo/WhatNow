<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class UserPasswordChanged extends Notification
{
    use Queueable;

    
    public function via($notifiable)
    {
        return ['mail'];
    }

    
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject(trans('passwords.changed.email.subject'))
            ->line(trans('passwords.changed.email.text', ['support_email' => config('mail.support_email')]));
    }
}
