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
        Schema::create(table: 'message_locks',
            callback: function (Blueprint $table): void {
                $table->uuid(column: 'id')->primary()->default(
                    DB::raw(value: 'uuidv7()')
                );

                $table->string(column: 'fingerprint', length: 64);
                
                $table->enum(
                    column: 'status',
                    allowed: [
                        'processing',
                        'processed'
                    ]
                )->default(
                    value: 'processing'
                );

                $table->timestampTz(column: 'expires_at', precision: 6);
                $table->timestampsTz(precision: 6);
            }
        );

        Schema::table(table: 'message_locks',
            callback: function (Blueprint $table): void {
                $table->comment(comment: 'Блокировка сообщений');

                $table->unique(columns: 'fingerprint');
                $table->index(columns: ['status', 'expires_at']);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'message_locks');
    }
};
