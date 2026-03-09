<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ResetRolesToFinalState extends Migration
{
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('api_full_access')->default(false)->after('sort');
        });
        $timestamp = now();

        $roles = [
            ['id' => 1, 'name' => 'API User',      'all' => 0, 'sort' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'api_full_access'=>0],
            ['id' => 2, 'name' => 'IFRC Support',  'all' => 1, 'sort' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'api_full_access'=>0],
            ['id' => 3, 'name' => 'IFRC Admin',    'all' => 0, 'sort' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'api_full_access'=>0],
            ['id' => 4, 'name' => 'NS Admin',      'all' => 0, 'sort' => 5, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'api_full_access'=>0],
            ['id' => 5, 'name' => 'NS Editor',     'all' => 0, 'sort' => 6, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'api_full_access'=>0],
            ['id' => 6, 'name' => 'Reviewer',      'all' => 0, 'sort' => 7, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'api_full_access'=>0],
            ['id' => 7, 'name' => 'API Admin',     'all' => 0, 'sort' => 2, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'api_full_access'=>1],
        ];

        // Move user_roles that reference any role not in the final set to role_id=1 (API User)
        $finalIds = array_column($roles, 'id');
        DB::table('user_roles')
            ->whereNotIn('role_id', $finalIds)
            ->update(['role_id' => 1]);

        // Truncate roles and permission_role pivot tables, then reinsert
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('roles')->insert($roles);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['api_full_access']);
        });
    }
}
