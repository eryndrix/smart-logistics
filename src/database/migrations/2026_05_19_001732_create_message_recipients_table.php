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
        Schema::create(table: 'message_recipients',
            callback: function (Blueprint $table): void {
                $table->uuid(column: 'id')->primary()->default(
                    DB::raw(value: 'uuidv7()')
                );

                $table->uuid(column: 'message_id');
                $table->uuid(column: 'subscriber_id');

                $table->string(column: 'address', length: 244);

                $table->enum(
                    column: 'status',
                    allowed: [
                        'queued',
                        'sent',
                        'delivered',
                        'failed'
                    ]
                )->default(
                    value: 'queued'
                );
                
                $table->text(column: 'error')->nullable();
                $table->timestamps(precision: 6);
            }
        );

        Schema::table(table: 'message_recipients',
            callback: function (Blueprint $table): void {
                $table->comment(comment: 'Доставка сообщений');

                $table->foreign(columns: 'message_id')
                    ->references(columns: 'id')
                    ->on(table: 'messages')
                    ->cascadeOnDelete();

                $table->unique(columns: ['message_id', 'subscriber_id']);
                $table->index(columns: ['subscriber_id', 'created_at']);

                $table->index(columns: 'status');
                $table->index(columns: 'created_at');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'message_recipients');
    }
};
