<?php

use App\Models\Access\Permission\Permission;
use App\Models\Access\Role\Role;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class InsertNewRole extends Migration
{
    
    public function up()
    {
        $role = new Role();
        $role->name = 'Reviewer';
        $role->sort = 6;
        $role->all = 0;
        $role->save();

        $permissionIds = Permission::whereIn('name', ['content-list','content-view','view-backend'])
            ->pluck('id')
            ->toArray();

        $role->permissions()
            ->sync( $permissionIds);

    }

    
    public function down()
    {
            }
}
