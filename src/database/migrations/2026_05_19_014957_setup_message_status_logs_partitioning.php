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
            DO $$
            DECLARE
                "pk_name" TEXT;
            BEGIN
                SELECT "conname" INTO "pk_name"
                FROM "pg_constraint"
                WHERE "conrelid" = '"message_status_logs"'::regclass
                AND "contype" = 'p';

                IF "pk_name" IS NOT NULL THEN
                    EXECUTE 'ALTER TABLE "message_status_logs" DROP CONSTRAINT "'
                        || "pk_name"
                        || '" CASCADE';
                END IF;
            END $$;
        SQL));

        DB::statement(query: rtrim(string: <<<'SQL'
            ALTER TABLE "message_status_logs"
            ADD PRIMARY KEY ("id", "occurred_at")
        SQL));

        DB::statement(query: rtrim(string: <<<'SQL'
            SELECT create_hypertable(
                'message_status_logs',
                'occurred_at',
                chunk_time_interval => INTERVAL '1 month',
                if_not_exists => TRUE
            );
        SQL));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(query: rtrim(string: <<<'SQL'
            SELECT drop_chunks(
                relation => 'message_status_logs',
                cascade_to_drop_set => TRUE
            );
        SQL));

        DB::statement(query: rtrim(string: <<<'SQL'
            ALTER TABLE "message_status_logs"
            DROP CONSTRAINT IF EXISTS "message_status_logs_pkey",
            DROP CONSTRAINT IF EXISTS "message_status_logs_pkey_1";
        SQL));

        DB::statement(query: rtrim(string: <<<'SQL'
            ALTER TABLE "message_status_logs"
            ADD PRIMARY KEY ("id");
        SQL));
    }
};
