<?php

namespace Tests\Browser;

use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use Tests\Browser\Pages\Login;
use Tests\Browser\Pages\WhatNow;
use Tests\DuskTestCase;

class WhatNowTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setup();

        static::closeAll();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function what_now_page_renders()
    {
        $this->markTestSkipped(
            'test needs looking at'
        );

        $user = \Database\Factories\UserFactory::new()->create();
        $user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $user->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->browse(function ($browser) use ($user) {
            $browser->visit(new Login)
                ->submit($user->email, 'secret')
                ->clickLink('What Now')
                ->Pause(300)
                ->assertPageIs(WhatNow::class)
                ->assertSee('Publish');
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function editor_cant_publish()
    {
        $this->markTestSkipped(
            'test needs looking at'
        );

        $user = \Database\Factories\UserFactory::new()->create();
        $user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $user->roles()->attach(Role::ROLE_NS_EDITOR);

        $this->browse(function ($browser) use ($user) {
            $browser->visit(new Login)
                ->submit($user->email, 'secret')
                ->clickLink('What Now')
                ->Pause(300)
                ->assertPageIs(WhatNow::class)
                ->assertDontSee('Publish');
        });
    }
}
