<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConfirmedRoleToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('confirmed_role')->default(true);
        });
        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropColumn('updated_at');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('confirmed_role');
        });
        Schema::table('user_roles', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });
    }
}
