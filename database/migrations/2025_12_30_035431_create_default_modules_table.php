<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('default_modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category', 100); // Ejemplo: 'erp', 'crm', 'tools', etc.
            $table->string('system_name', 100); // Ejemplo: 'SAP', 'Odoo', 'Salesforce', etc.
            $table->string('label'); // Nombre del módulo
            $table->text('description')->nullable(); // Descripción del módulo
            $table->string('type', 50)->default('link'); // link, modal, external
            $table->string('url', 500)->nullable(); // URL del módulo
            $table->string('target', 20)->default('_self'); // _self, _blank
            $table->string('modal_id', 100)->nullable(); // ID del modal si type es modal
            $table->text('icon')->nullable(); // SVG o clase de icono
            $table->string('icon_type', 20)->default('class'); // svg, class, image
            $table->string('background_color', 20)->default('#3b82f6'); // Color de fondo
            $table->boolean('is_featured')->default(false); // Destacado
            $table->string('group_name', 100)->nullable(); // Agrupación
            $table->integer('sort_order')->default(0); // Orden de visualización
            $table->boolean('is_active')->default(true); // Activo/Inactivo
            $table->timestamps();

            // Indices
            $table->index('category');
            $table->index('system_name');
            $table->index(['category', 'system_name']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_modules');
    }
};
