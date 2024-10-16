<?php

namespace App\Exceptions\Auth;

use Exception;


class UserConfirmationException extends Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (empty($message)) {
            $message = trans('auth.confirmation.not_found');
        }

        parent::__construct($message, $code, $previous);
    }
}
