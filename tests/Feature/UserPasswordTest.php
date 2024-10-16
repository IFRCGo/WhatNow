<?php

namespace Tests\Feature;

use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserPasswordTest extends TestCase
{
    /** @var \App\Models\Access\User\User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user->userProfile()->save(factory(UserProfile::class)->make());
    }

    /** @test */
    public function test_it_requires_existing_and_new_password()
    {
        $this->actingAs($this->user)
            ->patchJson('/api/settings/password', [
                'current_password' => 'somepassword'
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function test_it_returns_forbidden_if_existing_password_does_not_match()
    {
        $this->actingAs($this->user)
            ->patchJson('/api/settings/password', [
                'current_password' => 'incorrect_old_password',
                'password' => 'newpasswordislong',
                'password_confirmation' => 'newpasswordislong'
            ])
            ->assertStatus(403);

    }

    /** @test */
    public function test_it_updates_new_password()
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->json('PATCH', '/api/settings/password', [
            'current_password' => 'secret',
            'password' => 'updated10charslong',
            'password_confirmation' => 'updated10charslong'
        ], ['Authorization' => 'Bearer '. $token]);

        $response->assertStatus(200);

        // reload updated model from db
        $this->user->refresh();

        $this->assertTrue(Hash::check('updated10charslong', $this->user->password));
    }

    public function test_it_returns_a_new_jwt_token()
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->json('PATCH', '/api/settings/password', [
            'current_password' => 'secret',
            'password' => 'updated10charslong',
            'password_confirmation' => 'updated10charslong'
        ], ['Authorization' => 'Bearer ' . $token]);

        $data = $response->decodeResponseJson();

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'token_type']);
        $this->assertNotEquals($data['token'], $token);
    }
}
