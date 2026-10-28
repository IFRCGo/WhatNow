<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RoleMigrationsTest extends TestCase
{
    public function test_fresh_database_contains_the_final_roles(): void
    {
        $this->assertSame([
            1 => 'API User',
            2 => 'IFRC Support',
            3 => 'IFRC Admin',
            4 => 'NS Admin',
            5 => 'NS Editor',
            6 => 'Reviewer',
            7 => 'API Admin',
        ], DB::table('roles')->orderBy('id')->pluck('name', 'id')->all());
    }

    public function test_rename_preserves_an_existing_ifrc_role_with_a_different_id(): void
    {
        DB::table('roles')->where('id', 3)->update(['name' => 'Regional Admin']);
        DB::table('roles')->where('id', 2)->update(['name' => 'IFRC Admin']);
        $roles = DB::table('roles')->orderBy('id')->get()->toArray();

        $migration = $this->renameMigration();
        $migration->up();
        $migration->up();

        $this->assertEquals($roles, DB::table('roles')->orderBy('id')->get()->toArray());
    }

    public function test_legacy_gdpc_role_is_renamed_without_changing_its_id(): void
    {
        DB::table('roles')->where('id', 3)->update(['name' => 'GDPC Admin']);
        $migration = $this->renameMigration();

        $migration->up();

        $this->assertDatabaseHas('roles', ['id' => 3, 'name' => 'IFRC Admin']);
        $this->assertDatabaseHas('roles', ['id' => 4, 'name' => 'NS Admin']);

        $migration->down();

        $this->assertDatabaseHas('roles', ['id' => 3, 'name' => 'GDPC Admin']);
    }

    private function renameMigration(): \UpdateRoleNameIfrcAdmin
    {
        require_once database_path('migrations/2025_03_20_140938_update_role_name_ifrc_admin.php');

        return new \UpdateRoleNameIfrcAdmin();
    }
}
