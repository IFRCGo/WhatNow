<?php

namespace Tests\Feature;

use App\Models\Access\User\User;
use App\Notifications\Auth\UserNeedsConfirmation;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /** @test */
    public function can_register()
    {
        Notification::fake();

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

        Notification::assertSentTo(
            [$user],
            UserNeedsConfirmation::class
        );
    }
}
