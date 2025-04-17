<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class UpdateUserRolesAndRemoveRole3scAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('user_roles')
        ->where('role_id', 2)
        ->update(['role_id' => 1]);

        DB::table('roles')
            ->where('id', 2)
            ->delete();
        }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
