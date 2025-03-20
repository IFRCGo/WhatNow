<?php


use Illuminate\Database\Migrations\Migration;
use App\Models\Access\Permission\Permission;
use App\Models\Access\Role\Role;
use Carbon\Carbon;

class InsertEventTypeCreatePermission extends Migration
{
    
    public function up()
    {
        $permissionsToCreate = [
            'hz-type-create' => 'Hazard: Create Hazard Type',
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

        Role::where('name', 'IFRC Admin')->first()
            ->permissions()
            ->attach([
                $permissions['hz-type-create'],
            ]);

    }

    
    public function down()
    {
            }
}
