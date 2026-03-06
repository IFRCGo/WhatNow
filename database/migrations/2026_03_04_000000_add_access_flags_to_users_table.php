<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccessFlagsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_access_legacy_whatnow')->default(false)->after('confirmed_role');
            $table->boolean('can_access_preparedness_v2')->default(false)->after('can_access_legacy_whatnow');
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
            $table->dropColumn(['can_access_legacy_whatnow', 'can_access_preparedness_v2']);
        });
    }
}

