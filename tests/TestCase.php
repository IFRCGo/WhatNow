<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use CreatesApplication;

    protected array $sentMail = [];

    protected function setUp(): void
    {
        parent::setUp();

        config(['mail.mail_from' => 'sender@example.test']);
        $this->sentMail = [];
        $this->mock(\App\Classes\MailApi\MailApiClient::class)
            ->shouldReceive('send')
            ->andReturnUsing(function (\App\Classes\MailApi\Contract\MailApiClientRequest $request) {
                $this->sentMail[] = $request->jsonSerialize();
                return (object) ['success' => true];
            });
        $this->app->forgetInstance(\App\Classes\MailApi\MailApiService::class);
    }

    protected function assertConfirmationMailSentTo(string $email): void
    {
        $this->assertCount(1, $this->sentMail);
        $mail = $this->sentMail[0];
        $this->assertSame($email, base64_decode($mail['ToAsBase64']));
        $this->assertStringContainsString('/api/confirm/', base64_decode($mail['BodyAsBase64']));
        $this->assertTrue($mail['IsBodyHtml']);
    }

    protected function grantRolePermissions(int $roleId, array $permissions): void
    {
        \App\Models\Access\Role\Role::findOrFail($roleId)->permissions()->syncWithoutDetaching(
            \App\Models\Access\Permission\Permission::whereIn('name', $permissions)->pluck('id')
        );
    }
}
