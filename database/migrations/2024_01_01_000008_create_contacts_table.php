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
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('company_id');

            // Datos del contacto
            $table->string('name', 255);
            $table->string('last_name', 255)->nullable();
            $table->string('department', 255)->nullable();
            $table->string('position', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('extension', 20)->nullable();
            $table->string('mobile', 50)->nullable();

            // Avatar
            $table->string('avatar_url', 500)->nullable();

            // Estado y Orden
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);

            // Metadatos
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table->index('company_id');
            $table->index('department');
            $table->index('is_active');
            $table->index('deleted_at');
        });

        // Agregar columna status con tipo PostgreSQL nativo
        DB::statement("ALTER TABLE contacts ADD COLUMN status record_status DEFAULT 'active'");
        DB::statement("CREATE INDEX idx_contacts_status ON contacts(status)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
