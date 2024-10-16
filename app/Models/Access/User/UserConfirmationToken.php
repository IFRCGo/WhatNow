<?php

namespace App\Models\Access\User;

use App\Exceptions\Auth\UserConfirmationException;

class UserConfirmationToken
{
    
    private $token;

    
    public function __construct($token)
    {
        if (!is_string($token) || strlen($token) !== 32) {
            throw new UserConfirmationException();
        }

        $this->token = $token;
    }

    
    public static function generate()
    {
        return new self(md5(uniqid(mt_rand(), true)));
    }

    
    public function __toString()
    {
        return $this->getToken();
    }

    
    public function getToken()
    {
        return $this->token;
    }
}
