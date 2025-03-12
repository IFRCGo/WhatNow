<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPermissionApiStatsViewToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert([
            'id' => 18,
            'name' => 'api-stats-view',
            'display_name' => 'API Stats View',
            'sort' => 18,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('role_permissions')->insert([
            ['role_id' => 4, 'permission_id' => 18],
            ['role_id' => 3, 'permission_id' => 18]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('role_permissions')->where('permission_id', 18)->whereIn('role_id', [3, 4])->delete();
        DB::table('permissions')->where('id', 18)->delete();
    }
}
