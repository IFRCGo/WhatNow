<?php

namespace App\Notifications\Terms;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TermsUpdatedNotification extends Notification
{
    use Queueable;

    
    public function via($notifiable)
    {
        return ['mail'];
    }

    
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject(trans('terms.email.subject', ['app_name' => config('app.name')]))
            ->line(trans('terms.email.body'))
            ->action(trans('terms.email.action'), route('login'));
    }
}
