<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds is_primary_admin field to users table.
     * Only one admin per company can be primary (able to create other admins).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_primary_admin')->default(false)->after('status');
        });

        // Add partial unique index: only one primary admin per company among admin users
        DB::statement("
            CREATE UNIQUE INDEX idx_users_primary_admin_per_company
            ON users (company_id)
            WHERE is_primary_admin = true AND role = 'admin' AND deleted_at IS NULL
        ");

        // Add index for querying primary admins
        DB::statement("CREATE INDEX idx_users_is_primary_admin ON users(is_primary_admin) WHERE is_primary_admin = true");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS idx_users_primary_admin_per_company");
        DB::statement("DROP INDEX IF EXISTS idx_users_is_primary_admin");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_primary_admin');
        });
    }
};
