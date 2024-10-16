<?php

namespace App\Notifications\Auth;

use App\Models\Access\User\User;
use App\Models\Access\User\UserConfirmationToken;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;


class UserNeedsConfirmation extends Notification
{
    use Queueable;

    
    protected $confirmationToken;

    
    public function __construct(UserConfirmationToken $confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
    }

    
    public function via($notifiable)
    {
        return ['mail'];
    }

    
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject(trans('auth.confirmation.confirm'))
            ->greeting(trans('auth.confirmation.confirm'))
            ->line(trans('auth.confirmation.email_body'))
            ->action(trans('auth.confirmation.confirm'), route('confirm', (string) $this->confirmationToken));
    }
}
