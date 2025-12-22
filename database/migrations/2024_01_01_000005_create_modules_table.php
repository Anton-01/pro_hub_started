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
        Schema::create('modules', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('company_id');

            // Configuración del módulo
            $table->string('label', 255);
            $table->text('description')->nullable();
            $table->string('url', 500)->nullable();
            $table->string('target', 20)->default('_self'); // _self, _blank
            $table->string('modal_id', 100)->nullable();

            // Ícono (SVG o clase de ícono)
            $table->text('icon');
            $table->string('icon_type', 20)->default('svg'); // svg, class, image

            // Apariencia
            $table->string('highlight', 7)->nullable(); // Color de resaltado
            $table->string('background_color', 7)->nullable();
            $table->boolean('is_featured')->default(false);

            // Orden y organización
            $table->integer('sort_order')->default(0);
            $table->string('group_name', 100)->nullable();

            // Metadatos
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table->index('company_id');
            $table->index('sort_order');
            $table->index('deleted_at');
        });

        // Agregar columnas con tipos PostgreSQL nativos
        DB::statement("ALTER TABLE modules ADD COLUMN type module_type DEFAULT 'link'");
        DB::statement("ALTER TABLE modules ADD COLUMN status record_status DEFAULT 'active'");
        DB::statement("CREATE INDEX idx_modules_status ON modules(status)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
