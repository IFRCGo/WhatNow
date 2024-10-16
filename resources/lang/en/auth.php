<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'deactivated' => 'Your account has been deactivated. Please contact :support_email for assistance.',
    'confirmation' => [
        'already_confirmed' => 'Your account has been confirmed.',
        'confirm' => 'Confirm your account!',
        'created_confirm' => 'Your account was successfully created. We have sent you an e-mail to confirm your account.',
        'mismatch' => 'Your confirmation code does not match.',
        'not_found' => 'This account could not be confirmed, please contact your administrator and ask them to resend the confirmation email',
        'resend' => 'This account is not confirmed, please contact your administrator and ask them to resend the confirmation email',
        'success' => 'Your account has been successfully confirmed! Please set a password',
        'resent' => 'A new confirmation e-mail has been sent to the user\'s email address on file.',
        'email_body' => 'Your account has not been activated yet',
        'admin_welcome' => [
            'subject' => 'You have been invited to administrate :app_name',
            'greeting' => 'Welcome to :app_name',
            'email_body' => 'Your account has not been activated yet',
            'confirm' => 'Confirm your account!'
        ]
    ],
];
