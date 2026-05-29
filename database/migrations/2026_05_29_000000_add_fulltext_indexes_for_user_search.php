<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFulltextIndexesForUserSearch extends Migration
{
    
    public function up()
    {
        if (env('DB_CONNECTION') !== 'mysql') {
            return;
        }

        if (!$this->indexExists('users', 'users_email_fulltext_search')) {
            DB::statement('ALTER TABLE users ADD FULLTEXT users_email_fulltext_search(email)');
        }

        $this->dropIndexIfExists('user_profiles', 'user_profiles_search_fulltext');
        DB::statement('ALTER TABLE user_profiles ADD FULLTEXT user_profiles_search_fulltext(first_name, last_name)');
    }

    
    public function down()
    {
        if (env('DB_CONNECTION') !== 'mysql') {
            return;
        }

        $this->dropIndexIfExists('users', 'users_email_fulltext_search');
        $this->dropIndexIfExists('user_profiles', 'user_profiles_search_fulltext');
    }

    private function dropIndexIfExists(string $table, string $index)
    {
        if ($this->indexExists($table, $index)) {
            DB::statement("ALTER TABLE {$table} DROP INDEX {$index}");
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        return (bool) DB::select(
            DB::raw(
                "SHOW KEYS
                FROM {$table}
                WHERE Key_name='{$index}'"
            )
        );
    }
}
