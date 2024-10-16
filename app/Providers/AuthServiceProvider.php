<?php

namespace App\Providers;

use App\Classes\RcnApi\Entities\Instruction;
use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\History\History;
use App\Models\Terms;
use App\Policies\HistoryPolicy;
use App\Policies\InstructionManagementPolicy;
use App\Policies\RolePolicy;
use App\Policies\TermsPolicy;
use App\Policies\UserManagementPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    
    protected $policies = [
        User::class => UserManagementPolicy::class,
        Instruction::class => InstructionManagementPolicy::class,
        Role::class => RolePolicy::class,
        History::class => HistoryPolicy::class,
        Terms::class => TermsPolicy::class,
    ];

    
    public function boot()
    {
        $this->registerPolicies();

            }
}
