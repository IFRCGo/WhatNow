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

        $this->user = \Database\Factories\UserFactory::new()->create();
    }

    #[\PHPUnit\Framework\Attributes\Test]
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function fetch_the_current_user()
    {
        $this->actingAs($this->user)
            ->getJson('/api/users/me')
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['id', 'email']]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
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
