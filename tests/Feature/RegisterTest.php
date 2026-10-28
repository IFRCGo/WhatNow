<?php

namespace Tests\Feature;

use App\Models\Access\User\User;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function can_register()
    {
        $this->postJson('/api/register', [
            'email' => 'test@test.app',
            'password' => 'secretlongerthan10',
            'password_confirmation' => 'secretlongerthan10',
            'first_name' => 'Colin',
            'last_name' => 'Frizzell',
            'industry_type' => 'Gaming',
            'organisation' => '3SC',
            'country_code' => 'FR',
            'api_used_in' => 'Api used in blah blah',
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['id', 'email']);

        $user = User::orderBy('created_at')->first();

        $this->assertConfirmationMailSentTo($user->email);
    }
}
