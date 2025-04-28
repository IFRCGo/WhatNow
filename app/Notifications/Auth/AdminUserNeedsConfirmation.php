<?php

namespace App\Notifications\Auth;

use App\Models\Access\User\User;
use App\Models\Access\User\UserConfirmationToken;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Classes\MailApi\MailApiService;

class AdminUserNeedsConfirmation extends Notification
{
    use Queueable;

    
    protected $confirmationToken;
    private $mailApiService;

    
    public function __construct(UserConfirmationToken $confirmationToken, MailApiService $mailApiService)
    {
        $this->mailApiService = $mailApiService;
        $this->confirmationToken = $confirmationToken;
    }

    
    public function via($notifiable)
    {
        return [];
    }

    
    public function toMail($notifiable)
    {
        if (!$notifiable instanceof User) {
            throw new \BadFunctionCallException();
        }

        $route = route('confirm', (string) $this->confirmationToken);
        $html = $this->mailApiService->buildConfirmationTemplate($route);
        $email = $notifiable->email;

        $this->mailApiService->sendMail(
            $email,
            trans('auth.confirmation.admin_welcome.subject', ['app_name' => config('app.name')]),
            $html,
        );
    }
}
