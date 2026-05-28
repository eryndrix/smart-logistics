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
            CREATE OR REPLACE FUNCTION "log_message_recipient_status_change"()
            RETURNS TRIGGER AS $$
            BEGIN
                IF TG_OP = 'INSERT' THEN
                    INSERT INTO "message_status_logs" (
                        "message_recipient_id",
                        "from_status",
                        "to_status",
                        "reason",
                        "occurred_at"
                    ) VALUES (
                        NEW."id",
                        NULL,
                        NEW."status",
                        NULL,
                        NOW()
                    );
                    RETURN NEW;
                END IF;

                IF TG_OP = 'UPDATE' THEN
                    IF OLD."status" IS DISTINCT FROM NEW."status" THEN
                        INSERT INTO "message_status_logs" (
                            "message_recipient_id",
                            "from_status",
                            "to_status",
                            "reason",
                            "occurred_at"
                        ) VALUES (
                            NEW."id",
                            OLD."status",
                            NEW."status",
                            CASE 
                                WHEN NEW."status" = 'failed' THEN NEW."error"
                                ELSE NULL
                            END,
                            NOW()
                        );
                    END IF;
                    RETURN NEW;
                END IF;
                
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;
        SQL));

        DB::statement(query: rtrim(string: <<<'SQL'
            CREATE TRIGGER "recipient_status_change_trigger"
            AFTER INSERT OR UPDATE ON "message_recipients"
            FOR EACH ROW
            EXECUTE FUNCTION "log_message_recipient_status_change"();
        SQL));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(query: rtrim(string: <<<'SQL'
            DROP TRIGGER IF EXISTS "recipient_status_change_trigger" ON "message_recipients";
        SQL));

        DB::statement(query: rtrim(string: <<<'SQL'
            DROP FUNCTION IF EXISTS "log_message_recipient_status_change"();
        SQL));
    }
};