<?php

namespace App\Notifications\Terms;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Classes\MailApi\MailApiService;

class TermsUpdatedNotification extends Notification
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

        $route = route('login');
        $subject = trans('terms.email.subject', ['app_name' => config('app.name')]);
        $html = $this->mailApiService->buildTermsAndConditionsTemplate($route);
        $email = $notifiable->email;

        $this->mailApiService->sendMail(
            $email,
            $subject,
            $html,
        );
    }
}
