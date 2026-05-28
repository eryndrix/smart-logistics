<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::ensureExtensionExists(name: 'uuid-ossp');
        Schema::ensureExtensionExists(name: 'pgcrypto');
        Schema::ensureExtensionExists(name: 'pg_trgm');
        Schema::ensureExtensionExists(name: 'btree_gin');
        Schema::ensureExtensionExists(name: 'timescaledb');
        Schema::ensureExtensionExists(name: 'vector');
        Schema::ensureExtensionExists(name: 'btree_gist');
        Schema::ensureExtensionExists(name: 'unaccent');
    }
};
