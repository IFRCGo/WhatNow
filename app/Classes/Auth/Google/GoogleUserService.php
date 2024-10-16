<?php

namespace App\Classes\Auth\Google;

use \InvalidArgumentException;
use App\Exceptions\Auth\GoogleUserException;
use Laravel\Socialite\Two\User;


class GoogleUserService
{
    
    public function getUserFromToken(string $token = null)
    {
        $opts = array(
            'http' => array(
                'method' => "GET",
            )
        );

        $query = http_build_query(['id_token' => $token]);
        $context = stream_context_create($opts);
        $response = file_get_contents('https://oauth2.googleapis.com/tokeninfo?' . $query, false, $context);

        if (!$response) {
            throw new GoogleUserException('User verification failed');
        }

        $data = json_decode($response, true);

                if (config('services.google.client_id') !== $data['aud']) {
            throw new GoogleUserException('Audience does not match this application');
        }

        $user = new User();
        $user->map([
            'id' => $data['sub'],
            'nickname' => $data['given_name'],
            'name' => $data['name'],
            'avatar' => $data['picture'],
            'email' => $data['email'],
            'user' => $data
        ]);

        return $user;
    }
}
