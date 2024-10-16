<?php

namespace App\Exceptions\Auth;

use Exception;


class UserConfirmationMismatchException extends UserConfirmationException
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (empty($message)) {
            $message = trans('auth.confirmation.mismatch');
        }

        parent::__construct($message, $code, $previous);
    }
}
