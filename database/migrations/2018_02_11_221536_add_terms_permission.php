<?php

use App\Models\Access\Permission\Permission;
use App\Models\Access\Role\Role;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddTermsPermission extends Migration
{
    
    public function up()
    {
        $permission = new Permission();
        $permission->name = 'terms-update';
        $permission->display_name = 'Terms and Conditions: Update';
        $permission->sort = 16;
        $permission->created_at = Carbon::now();
        $permission->updated_at = Carbon::now();
        $permission->save();

        Role::find(3)        ->permissions()
            ->attach([
                $permission->id,
            ]);
    }

    
    public function down()
    {
            }
}
