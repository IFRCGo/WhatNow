<?php

namespace Tests\Feature;

use App\Models\Access\User\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /** @var \App\Models\Access\User\User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function authenticate()
    {
        $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'secret',
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['token', 'expires_in'])
        ->assertJson(['token_type' => 'bearer']);
    }

    /** @test */
    public function fetch_the_current_user()
    {
        $this->actingAs($this->user)
            ->getJson('/api/users/me')
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['id', 'email']]);
    }

    /** @test */
    public function log_out()
    {
        $token = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'secret',
        ])->json()['token'];

        $this->postJson("/api/logout?token=$token")
            ->assertSuccessful();

        $this->getJson("/api/users/me")
            ->assertStatus(401);
    }
}
