<?php

namespace App\Classes\MailApi;

use App\Classes\MailApi\MailApiClient;
use App\Classes\MailApi\Contract\MailApiClientRequest;
class MailApiService
{
    protected $mailApiClient;

    public function __construct(MailApiClient $mailApiClient)
    {
        $this->mailApiClient = $mailApiClient;
    }

    public function sendMail($to, $subject, $body, $isBulk = false  )
    {
        $from = config('mail.mail_from');

        $request = new MailApiClientRequest(
            $from,
            $to,
            $subject,
            $body,
            true,
            $isBulk
        );

        return $this->mailApiClient->send($request);
    }

    public function buildMailTemplate(
        ?string $title = 'IFRC National Society Preparedness Messages',
        array $contentParagraphs,
        ?string $buttonText = null,
        ?string $buttonUrl = null,
        ?string $footerText = 'IFRC National Society Preparedness Messages'
    ): string
    {
        $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>' . htmlspecialchars($title) . '</title>
    </head>
    <body style="font-family: Avenir, Helvetica, sans-serif; background-color: #2b2d2f; margin: 0; padding: 20px; color: #9fa3a9; line-height: 1.4;">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background-color: #292929; border-radius: 8px; overflow: hidden;">
            <tr>
                <td align="center" style="padding: 25px 10px; background-color: #292929;">
                    <a href="http://whatnow.jazusoft.com" style="text-decoration: none; color: #bbbfc3; font-size: 19px; font-weight: bold;">
                        IFRC Safety Messages Database
                    </a>
                </td>
            </tr>
            <tr>
                <td style="padding: 35px; background-color: #292929;">';

        $html .= '<h1 style="color: #d6d9db; font-size: 19px; font-weight: bold; margin-top: 0; text-align: left;">' . htmlspecialchars($title) . '</h1>';

        foreach ($contentParagraphs as $paragraph) {
            $html .= '<p style="font-size: 16px; margin-top: 0; text-align: left;">' . nl2br(htmlspecialchars($paragraph)) . '</p>';
        }

        if ($buttonText && $buttonUrl) {
            $html .= '
                <div style="text-align: center; margin: 30px 0;">
                    <a href="' . htmlspecialchars($buttonUrl) . '"
                       style="background-color: #000000; color: #ffffff; padding: 8px 16px; border-radius: 60px; text-decoration: none; display: inline-block; font-weight: bold;">
                        ' . htmlspecialchars($buttonText) . '
                    </a>
                </div>';
        }

        $html .= '</td>
            </tr>
            <tr>
                <td align="center" style="padding: 25px 10px; font-size: 12px; color: #aeaeae; background-color: #292929;">
                    ' . htmlspecialchars($footerText) . '
                </td>
            </tr>
        </table>
    </body>
    </html>';

        return $html;
    }

    public function buildResetPasswordTemplate(
        string $resetLink
    ): string
    {
        return $this->buildMailTemplate(
            'Password Reset Request',
            [
                "Hello!",
                "You are receiving this email because we received a password reset request for your account.",
                "If you did not request a password reset, no further action is required."
            ],
            'Reset Password',
            $resetLink,
            'IFRC National Society Preparedness Messages'
        );
    }

    public function buildConfirmationTemplate(
        string $confirmationLink
    ): string
    {
        return $this->buildMailTemplate(
            'Confirm your account!',
            [
                "Confirm your account!",
                "Your account has not been activated yet",
                "If you’re having trouble clicking the Confirm your account! button, copy and paste the URL below into your web browser: $confirmationLink",
            ],
            'Confirm your Account!',
            $confirmationLink,
            'IFRC National Society Preparedness Messages'
        );
    }

    public function buildPasswordChangedTemplate(): string
    {
        $supportEmail = config('mail.support_email');
        return $this->buildMailTemplate(
            'Your password has been updated',
            [
                "If you did this, please ignore this email. However if you think someone else knows or has changed your password, please email $supportEmail to recover your account",
            ],
            null,
            null,
            'IFRC National Society Preparedness Messages'
        );
    }

    public function buildTermsAndConditionsTemplate(
        string $termsLink
    ): string
    {
        return $this->buildMailTemplate(
            'Terms and Conditions Updated',
            [
                "We have updated our terms and conditions for using our API. By continuing to use the service, you agree to the new terms and conditions.",
            ],
            'Log in to view the new Terms and Conditions',
            $termsLink,
            'IFRC National Society Preparedness Messages'
        );
    }

}
