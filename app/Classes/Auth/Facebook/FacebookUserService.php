<?php

namespace App\Classes\Auth\Facebook;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;
use Laravel\Socialite\Facades\Socialite;
use \InvalidArgumentException;
use App\Exceptions\Auth\FacebookUserException;


class FacebookUserService
{
    
    protected $provider;

    
    protected $fields = ['name', 'first_name', 'last_name', 'email'];

    
    public function __construct()
    {
        $this->provider = Socialite::driver('facebook');
        $this->provider->stateless();
        $this->provider->fields($this->fields);
    }

    
    public function getUserFromToken(string $token = null)
    {
        if (!$token) {
            throw new InvalidArgumentException('Token must be a valid Facebook user token string');
        }

        try {
            $user = $this->provider->userFromToken($token);

            if (!$user) {
                throw new FacebookUserException('Facebook User could not be found', 404);
            }

            return $user;
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody(), true);
            if (isset($response['error']) && isset($response['error']['message'])) {
                throw new FacebookUserException($response['error']['message'], $e->getCode(), $e);
            }

            throw new FacebookUserException('Invalid request to Facebook User Service', $e->getCode(), $e);
        } catch (ServerException $e) {
            $response = json_decode($e->getResponse()->getBody(), true);
            if (isset($response['error']) && isset($response['error']['message'])) {
                throw new FacebookUserException($response['error']['message'], $e->getCode(), $e);
            }

            throw new FacebookUserException('Server issue to Facebook User Service', $e->getCode(), $e);
        } catch (TransferException $e) {
            throw new FacebookUserException('Transfer issue with Facebook User Service', $e->getCode(), $e);
        }
    }
}
