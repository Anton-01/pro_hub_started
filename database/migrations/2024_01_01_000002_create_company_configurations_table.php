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
        Schema::create('company_configurations', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('company_id');

            // Configuración de Logo
            $table->string('logo_url', 500)->nullable();
            $table->integer('logo_width')->default(80);
            $table->integer('logo_height')->default(80);
            $table->string('logo_mime_type', 100)->nullable();
            $table->integer('logo_file_size')->nullable();
            $table->string('logo_original_name', 255)->nullable();

            // Favicon
            $table->string('favicon_url', 500)->nullable();

            // Colores del tema
            $table->string('primary_color', 7)->default('#c9a227');      // Color dorado (gold)
            $table->string('secondary_color', 7)->default('#0a1744');    // Azul oscuro
            $table->string('accent_color', 7)->default('#f59e0b');       // Naranja
            $table->string('background_color', 7)->default('#0d1b4c');   // Fondo
            $table->string('text_color', 7)->default('#ffffff');         // Texto
            $table->string('error_color', 7)->default('#ef4444');        // Error
            $table->string('success_color', 7)->default('#10b981');      // Éxito
            $table->string('warning_color', 7)->default('#f59e0b');      // Advertencia
            $table->string('module_bg_color', 7)->default('#1a3a8f');    // Fondo de módulos
            $table->string('module_hover_color', 7)->default('#2548a8'); // Hover de módulos

            // Configuración adicional
            $table->string('header_text', 255)->nullable();
            $table->string('footer_text', 500)->nullable();
            $table->boolean('show_calendar')->default(true);
            $table->boolean('show_news_ticker')->default(true);
            $table->boolean('show_contacts')->default(true);

            // SEO
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 500)->nullable();

            // Metadatos
            $table->timestamps();

            // Relación y unicidad
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table->unique('company_id');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_configurations');
    }
};
