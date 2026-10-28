<?php

namespace Tests\Feature;

use App\Models\Access\User\User;
use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

class Laravel13CompatibilityTest extends TestCase
{
    use CreatesApplication;

    public function test_health_route_boots_on_laravel_13(): void
    {
        $this->assertStringStartsWith('13.', $this->app->version());
        $this->getJson('/api/health')->assertOk()->assertExactJson(['status' => 'ok']);
    }

    public function test_protected_routes_reject_requests_without_a_jwt(): void
    {
        $this->getJson('/api/users/me')->assertUnauthorized();
    }

    public function test_cors_preflight_accepts_bearer_authorization_headers(): void
    {
        $this->withHeaders([
            'Origin' => 'https://frontend.example',
            'Access-Control-Request-Method' => 'GET',
            'Access-Control-Request-Headers' => 'authorization,content-type',
        ])->options('/api/users/me')
            ->assertSuccessful()
            ->assertHeader('Access-Control-Allow-Origin', '*');
    }

    public function test_password_and_login_dates_keep_the_existing_json_format(): void
    {
        $user = new User();
        $user->password_updated_at = '2026-10-22 12:34:56';
        $user->last_logged_in_at = '2026-10-22 12:34:56';

        $this->assertInstanceOf(\DateTimeInterface::class, $user->password_updated_at);
        $this->assertInstanceOf(\DateTimeInterface::class, $user->last_logged_in_at);
        $this->assertSame('2026-10-22 12:34:56', $user->attributesToArray()['password_updated_at']);
    }

    public function test_translations_still_use_the_existing_resources_directory(): void
    {
        $this->assertSame(resource_path('lang'), lang_path());
        $translations = json_decode(file_get_contents(resource_path('lang/en.json')), true);
        $this->getJson('/api/translations/en')->assertOk()->assertJson(['ok' => $translations['ok']]);
    }
}
