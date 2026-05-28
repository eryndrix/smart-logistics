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
        Schema::create(table: 'roles',
            callback: function (Blueprint $table): void {
                $table->uuid(column: 'id')->primary()->default(
                    DB::raw(value: 'uuidv7()')
                );

                $table->string(column: 'name', length: 15);
                $table->string(column: 'slug', length: 20)->unique();
                
                $table->timestamps(precision: 6);

            }
        );

        Schema::table(table: 'roles',
            callback: function (Blueprint $table): void {
                $table->comment(comment: 'Роли');
                $table->index(columns: 'created_at');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'roles');
    }
};
