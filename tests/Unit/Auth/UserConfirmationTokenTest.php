<?php

namespace Tests\Unit\Auth;

use App\Exceptions\Auth\UserConfirmationException;
use App\Models\Access\User\UserConfirmationToken;
use Tests\TestCase;

class UserConfirmationTokenTest extends TestCase
{
    public function test_it_creates_token()
    {
        $tokenString = md5('test');

        $token = new UserConfirmationToken($tokenString);

        $this->assertInstanceOf(UserConfirmationToken::class, $token);
    }

    public function test_it_is_stringifiable()
    {
        $tokenString = md5('test');

        $token = new UserConfirmationToken($tokenString);

        $this->assertInternalType('string', (string) $token);
    }

    public function test_it_invalidates_token()
    {
        $this->expectException(UserConfirmationException::class);

        $tokenString = sha1('test');

        new UserConfirmationToken($tokenString);
    }

    public function test_it_generates_token()
    {
        $tokenString = UserConfirmationToken::generate();

        $this->assertInstanceOf(UserConfirmationToken::class, $tokenString);
    }
}
