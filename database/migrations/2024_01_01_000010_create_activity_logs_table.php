<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('company_id')->nullable();
            $table->uuid('user_id')->nullable();

            // Acción
            $table->string('action', 100); // login, logout, create, update, delete, etc.
            $table->string('entity_type', 100)->nullable(); // users, modules, news, etc.
            $table->uuid('entity_id')->nullable();

            // Detalles
            $table->text('description')->nullable();
            $table->jsonb('old_values')->nullable();
            $table->jsonb('new_values')->nullable();

            // Contexto
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Metadatos
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->index('company_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('entity_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
