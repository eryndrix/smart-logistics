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
            CREATE INDEX "message_recipients_failed_idx"
            ON "message_recipients"
            ("created_at" DESC)
            WHERE "status" = 'failed'
        SQL));

        DB::statement(query: rtrim(string: <<<'SQL'
            CREATE INDEX "message_recipients_by_recipient_created_at_idx"
            ON "message_recipients"
            ("subscriber_id", "created_at" DESC)
            INCLUDE ("status", "error")
        SQL));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(query: rtrim(string: <<<'SQL'
            DROP INDEX IF EXISTS "message_recipients_failed_idx"
        SQL));

        DB::statement(query: rtrim(string: <<<'SQL'
            DROP INDEX IF EXISTS "message_recipients_by_recipient_created_at_idx"
        SQL));
    }
};
