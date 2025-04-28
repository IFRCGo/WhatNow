<?php

namespace App\Notifications\Auth;

use App\Classes\MailApi\MailApiService;
use App\Models\Access\User\UserConfirmationToken;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;


class UserNeedsConfirmation extends Notification
{
    use Queueable;

    
    protected $confirmationToken;
    protected $mailApiService;

    
    public function __construct(UserConfirmationToken $confirmationToken, MailApiService $mailApiService)
    {
        $this->confirmationToken = $confirmationToken;
        $this->mailApiService = $mailApiService;
    }

    
    public function via($notifiable)
    {
        return [];
    }

    
    public function toMail($notifiable)
    {
        $route = route('confirm', ['token' => (string) $this->confirmationToken]);
        $html = $this->mailApiService->buildConfirmationTemplate($route);
        $email = $notifiable->email;

        $this->mailApiService->sendMail(
            $email,
            trans('auth.confirmation.confirm'),
            $html,
        );
    }
}
