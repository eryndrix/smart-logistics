<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(table: 'message_status_logs',
            callback: function (Blueprint $table): void {
                $table->uuid(column: 'id')->primary()->default(
                    value: DB::raw(value: 'uuidv7()')
                );

                $table->uuid(column: 'message_recipient_id');

                $table->enum(
                    column: 'from_status',
                    allowed: [
                        'queued',
                        'sent',
                        'delivered',
                        'failed',
                    ]
                )->nullable();

                $table->enum(
                    column: 'to_status',
                    allowed: [
                        'queued',
                        'sent',
                        'delivered',
                        'failed',
                    ]
                );

                $table->text(column: 'reason')->nullable();

                $table->timestampTz(
                    column: 'occurred_at',
                    precision: 6
                )->useCurrent();
            }
        );

        Schema::table(table: 'message_status_logs',
            callback: function (Blueprint $table): void {
                $table->comment(comment: 'Журнал статусов');

                $table->foreign(columns: 'message_recipient_id')
                    ->references(columns: 'id')
                    ->on(table: 'message_recipients')
                    ->cascadeOnDelete();

                $table->index(columns: [
                    'message_recipient_id',
                    'occurred_at',
                ]);

                $table->index(columns: 'occurred_at');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'message_status_logs');
    }
};
