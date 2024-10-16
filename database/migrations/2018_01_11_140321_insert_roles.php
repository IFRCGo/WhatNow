<?php

use App\Models\Access\Permission\Permission;
use App\Models\Access\Role\Role;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class InsertRoles extends Migration
{
    
    public function up()
    {
        $rolesToCreate = [
            'api-user' => 'API User',
            '3sc-admin' => '3SC Admin',
            'gdpc-admin' => 'GDPC Admin',
            'ns-admin' => 'NS Admin',
            'ns-editor' => 'NS Editor',
        ];

        $counter = 1;
        $roles = [];
        foreach ($rolesToCreate as $name => $roleName) {
            $role = new Role();
            $role->name = $roleName;
            $role->sort = $counter;
            $role->all = ($name === '3sc-admin') ? 1 : 0;
            $role->save();
            $roles[$name] = $role;
            $counter++;
        }

        $permissionsToCreate = [
            'view-backend' => 'View Backend',
            'users-view' => 'Users: View details',
            'users-list' => 'Users: List all users',
            'users-create' => 'Users: Create new users',
            'users-edit' => 'Users: Edit users',
            'users-delete' => 'Users: Delete users',
            'users-deactivate' => 'Users: Deactivate users',
            'users-reactivate' => 'Users: Reactivate users',
            'content-publish' => 'Content: Publish content',
            'content-create' => 'Content: Create content',
            'content-edit' => 'Content: Edit content',
            'content-delete' => 'Content: Delete content',
            'content-view' => 'Content: View content',
            'content-list' => 'Content: List content',
        ];

        $counter = 1;
        $permissions = [];
        foreach ($permissionsToCreate as $name => $displayName) {
            $permission = new Permission();
            $permission->name = $name;
            $permission->display_name = $displayName;
            $permission->sort = $counter;
            $permission->created_at = Carbon::now();
            $permission->updated_at = Carbon::now();
            $permission->save();
            $counter++;
            $permissions[$name] = $permission->id;
        }

        Role::find($roles['gdpc-admin'])->first()
            ->permissions()
            ->sync([
                $permissions['view-backend'],
                $permissions['users-view'],
                $permissions['users-list'],
                $permissions['users-create'],
                $permissions['users-edit'],
                $permissions['users-delete'],
                $permissions['users-deactivate'],
                $permissions['users-reactivate'],
                $permissions['content-publish'],
                $permissions['content-create'],
                $permissions['content-edit'],
                $permissions['content-delete'],
                $permissions['content-view'],
                $permissions['content-list'],
            ]);

        Role::find($roles['ns-admin'])->first()
            ->permissions()
            ->sync([
                $permissions['view-backend'],
                $permissions['content-publish'],
                $permissions['content-create'],
                $permissions['content-edit'],
                $permissions['content-delete'],
                $permissions['content-view'],
                $permissions['content-list'],
            ]);

        Role::find($roles['ns-editor'])->first()
            ->permissions()
            ->sync([
                $permissions['view-backend'],
                $permissions['content-create'],
                $permissions['content-edit'],
                $permissions['content-delete'],
                $permissions['content-view'],
                $permissions['content-list'],
            ]);
    }

    
    public function down()
    {
            }
}
