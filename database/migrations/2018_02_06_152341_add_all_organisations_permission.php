<?php

use App\Models\Access\Permission\Permission;
use App\Models\Access\Role\Role;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddAllOrganisationsPermission extends Migration
{
    
    public function up()
    {
        $permission = new Permission();
        $permission->name = 'organisations_all';
        $permission->display_name = 'Organisations: Access all';
        $permission->sort = 15;
        $permission->created_at = Carbon::now();
        $permission->updated_at = Carbon::now();
        $permission->save();

        Role::find(3)         ->permissions()
            ->attach([
                $permission->id,
            ]);
    }

    
    public function down()
    {
            }
}
