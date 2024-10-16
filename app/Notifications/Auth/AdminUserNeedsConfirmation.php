<?php

namespace App\Notifications\Auth;

use App\Models\Access\User\User;
use App\Models\Access\User\UserConfirmationToken;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;


class AdminUserNeedsConfirmation extends Notification
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
        if (!$notifiable instanceof User) {
            throw new \BadFunctionCallException();
        }

        return (new MailMessage())
            ->subject(trans('auth.confirmation.admin_welcome.subject', ['app_name' => config('app.name')]))
            ->greeting(trans('auth.confirmation.admin_welcome.greeting', ['app_name' => config('app.name')]))
            ->line(trans('auth.confirmation.admin_welcome.email_body'))
            ->action(trans('auth.confirmation.admin_welcome.confirm'), route('confirm', (string) $this->confirmationToken));
    }
}
