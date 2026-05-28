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
        Schema::create(table: 'personal_access_tokens',
            callback: function (Blueprint $table): void {
                $table->uuid(column: 'id')->primary()->default(
                    DB::raw(value: 'uuidv7()')
                );

                $table->uuidMorphs(name: 'tokenable');
                $table->string(column: 'name', length: 30);
                $table->string(column: 'token', length: 64)->unique();
                $table->jsonb(column: 'abilities')->nullable();

                $table->timestamp(column: 'last_used_at')->nullable();
                $table->timestamp(column: 'expires_at')->nullable();

                $table->timestamps(precision: 6);
            }
        );

        Schema::table(table: 'personal_access_tokens',
            callback: function (Blueprint $table): void {
                $table->comment(comment: 'Токены');

                $table->foreign(columns: 'tokenable_id')
                    ->references(columns: 'id')
                    ->on(table: 'users')
                    ->cascadeOnDelete();

                $table->index(columns: 'expires_at');
                $table->index(columns: 'created_at');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
