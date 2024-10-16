<?php

namespace Tests\Feature;

use App\Classes\Auth\Google\GoogleUserService;
use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use Laravel\Socialite\Two\GoogleProvider;
use Mockery;
use Tests\TestCase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\FacebookProvider;
use Laravel\Socialite\Two\User as SocialUser;

class OAuthTest extends TestCase
{
    public function test_it_creates_and_authenticates_a_new_user_from_facebook_login()
    {
        $this->mockFacebookTokenRequest();

        $this->assertDatabaseMissing('users', [
            'email' => 'someuser@email.com',
        ]);

        $this->postJson('/api/login/facebook', [
            'token' => 'FacebookOauthToken'
        ])->assertSuccessful()
            ->assertJsonStructure(['token', 'expires_in'])
            ->assertJson(['token_type' => 'bearer']);

        $this->assertDatabaseHas('users', [
            'email' => 'someuser@email.com',
        ]);
    }

    public function test_it_authenticates_an_existing_facebook_user_from_facebook_login()
    {
        $this->mockFacebookTokenRequest();

        $user = factory(User::class)->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());
        $user->oauthProviders()->create([
            'provider' => 'facebook',
            'provider_user_id' => '10155139028074349',
            'access_token' => 'FacebookOauthToken',
            'refresh_token' => null
        ]);

        $this->post('/api/login/facebook', [
            'token' => 'FacebookOauthToken'
        ])->assertSuccessful()
            ->assertJsonStructure(['token', 'expires_in'])
            ->assertJson(['token_type' => 'bearer']);

        $this->assertDatabaseHas('oauth_providers', [
            'user_id' => $user->id,
            'provider_user_id' => '10155139028074349'
        ]);
    }

    public function test_it_links_an_existing_conventional_user_with_facebook_account()
    {
        $this->mockFacebookTokenRequest();
        $user = factory(User::class)->create([
            // existing user has same email as new social account
            'email' => 'someuser@email.com'
        ]);
        $user->userProfile()->save(factory(UserProfile::class)->make());

        $this->postJson('/api/login/facebook', [
            'token' => 'FacebookOauthToken'
        ])->assertSuccessful()
            ->assertJsonStructure(['token', 'expires_in'])
            ->assertJson(['token_type' => 'bearer']);

        $this->assertDatabaseHas('oauth_providers', [
            'user_id' => $user->id,
            'provider_user_id' => '10155139028074349'
        ]);
    }

    public function test_it_creates_and_authenticates_a_new_user_from_google_login()
    {
        $this->mockGoogleTokenRequest();

        $this->assertDatabaseMissing('users', [
            'email' => 'someuser@email.com',
        ]);

        $this->postJson('/api/login/google', [
            'token' => 'googleIdToken'
        ])->assertSuccessful()
            ->assertJsonStructure(['token', 'expires_in'])
            ->assertJson(['token_type' => 'bearer']);

        $this->assertDatabaseHas('users', [
            'email' => 'someuser@email.com',
        ]);
    }

    public function test_it_authenticates_an_existing_google_user_from_google_login()
    {
        $this->mockGoogleTokenRequest();

        $user = factory(User::class)->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());
        $user->oauthProviders()->create([
            'provider' => 'google',
            'provider_user_id' => '10155139028074349',
            'access_token' => null,
            'refresh_token' => null
        ]);

        $this->post('/api/login/google', [
            'token' => 'GoogleIdToken'
        ])->assertSuccessful()
            ->assertJsonStructure(['token', 'expires_in'])
            ->assertJson(['token_type' => 'bearer']);

        $this->assertDatabaseHas('oauth_providers', [
            'user_id' => $user->id,
            'provider_user_id' => '10155139028074349'
        ]);
    }

    private function mockGoogleTokenRequest()
    {
        $googleUser = (new SocialUser)->map([
            'name' => 'Some User',
            'first_name' => 'Some',
            'last_name' => 'User',
            'email' => 'someuser@email.com',
            'id' => '10155139028074349'
        ]);

        // mock our user service and replace the ioc instance
        $gService = Mockery::mock(GoogleUserService::class);
        $gService->shouldReceive('getUserFromToken')->andReturn($googleUser);

        $this->app->instance(GoogleUserService::class, $gService);
    }


    private function mockFacebookTokenRequest()
    {
        // Mock internals of fb user profile request with token
        $fbUser = (new SocialUser)->map([
            'name' => 'Some User',
            'first_name' => 'Some',
            'last_name' => 'User',
            'email' => 'someuser@email.com',
            'id' => '10155139028074349'
        ]);

        $fbProvider = Mockery::mock(FacebookProvider::class);
        $fbProvider->shouldReceive('stateless');
        $fbProvider->shouldReceive('fields');
        $fbProvider->shouldReceive('userFromToken')->andReturn($fbUser);

        Socialite::shouldReceive('driver')->withArgs(['facebook'])->andReturn($fbProvider);
    }
}
