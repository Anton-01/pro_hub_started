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
        Schema::create('banner_images', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('company_id');

            // Contenido
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();

            // Archivo
            $table->string('url', 500)->nullable();
            $table->string('image_path', 500);
            $table->string('alt_text', 255)->nullable();

            // Metadatos de imagen
            $table->string('original_name', 255)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->integer('file_size')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();

            // Link opcional
            $table->string('link_url', 500)->nullable();
            $table->string('link_target', 20)->default('_self');

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
            $table->index('is_active');
            $table->index('deleted_at');
        });

        // Agregar columna status con tipo PostgreSQL nativo
        DB::statement("ALTER TABLE banner_images ADD COLUMN status record_status DEFAULT 'active'");
        DB::statement("CREATE INDEX idx_banner_images_status ON banner_images(status)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banner_images');
    }
};
