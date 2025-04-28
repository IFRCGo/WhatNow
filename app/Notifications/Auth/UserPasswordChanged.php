<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Classes\MailApi\MailApiService;

class UserPasswordChanged extends Notification
{
    use Queueable;
    private $mailApiService;

    public function __construct(MailApiService $mailApiService)
    {
        $this->mailApiService = $mailApiService;
    }
    
    public function via($notifiable)
    {
        return [];
    }

    
    public function toMail($notifiable)
    {

        $html = $this->mailApiService->buildPasswordChangedTemplate();
        $email = $notifiable->email;

        $this->mailApiService->sendMail(
            $email,
            trans('passwords.changed.email.subject'),
            $html,
        );
    }
}
