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
        Schema::create('news', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('company_id');

            // Contenido
            $table->text('text');
            $table->string('url', 500)->nullable();

            // Programación
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('ends_at')->nullable();

            // Orden y prioridad
            $table->integer('sort_order')->default(0);
            $table->boolean('is_priority')->default(false);

            // Metadatos
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->index('company_id');
            $table->index('starts_at');
            $table->index('ends_at');
            $table->index('deleted_at');
        });

        // Agregar columna status con tipo PostgreSQL nativo
        DB::statement("ALTER TABLE news ADD COLUMN status record_status DEFAULT 'active'");
        DB::statement("CREATE INDEX idx_news_status ON news(status)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
