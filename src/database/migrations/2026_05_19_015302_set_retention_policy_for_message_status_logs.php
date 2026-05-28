<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(query: rtrim(string: <<<'SQL'
            SELECT add_retention_policy(
                'message_status_logs',
                INTERVAL '1 year'
            );
        SQL));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(query: rtrim(string: <<<'SQL'
            SELECT remove_retention_policy(
                'message_status_logs'
            );
        SQL));
    }
};
