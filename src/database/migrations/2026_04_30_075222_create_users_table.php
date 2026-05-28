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
        Schema::create(table: 'users',
            callback: function (Blueprint $table): void {
                $table->uuid(column: 'id')->primary()->default(
                    DB::raw(value: 'uuidv7()')
                );

                $table->string(column: 'name', length: 61);
                $table->string(column: 'email', length: 244)->unique();
                
                $table->timestamp(
                    column: 'email_verified_at',
                    precision: 6
                )->nullable();

                $table->uuid(column: 'role_id');
                $table->string(column: 'password', length: 64);

                $table->rememberToken();
                $table->timestamps(precision: 6);
            }
        );

        Schema::table(table: 'users',
            callback: function (Blueprint $table): void {
                $table->comment(comment: 'Пользователи');

                $table->foreign(columns: 'role_id')
                    ->references(columns: 'id')
                    ->on(table: 'roles')
                    ->cascadeOnDelete();

                $table->index(columns: 'role_id');
                $table->index(columns: 'created_at');
            }
        );

        Schema::create(table: 'password_reset_tokens', 
            callback: function (Blueprint $table): void {
                $table->string(column: 'email', length: 244)->primary();
                $table->string(column: 'token', length: 255);
                $table->timestamp(column: 'created_at', precision: 6)->nullable();
            }
        );

        Schema::create(table: 'sessions', 
            callback: function (Blueprint $table): void {
                $table->uuid(column: 'id')->primary()->default(
                    DB::raw(value: 'uuidv7()')
                );
                $table->foreignUuid(column: 'user_id')->nullable()->index();
                $table->string(column: 'ip_address', length: 45)->nullable();
                $table->text(column: 'user_agent')->nullable();
                $table->longText(column: 'payload');
                $table->integer(column: 'last_activity')->index();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'users');
        Schema::dropIfExists(table: 'password_reset_tokens');
        Schema::dropIfExists(table: 'sessions');
    }
};
