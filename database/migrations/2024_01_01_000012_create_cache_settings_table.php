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
        Schema::create('cache_settings', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('company_id')->nullable();

            // Configuración de TTL (en segundos)
            $table->integer('modules_ttl')->default(600);      // 10 minutos
            $table->integer('contacts_ttl')->default(600);     // 10 minutos
            $table->integer('events_ttl')->default(600);       // 10 minutos
            $table->integer('news_ttl')->default(60);          // 1 minuto (más frecuente por cambios)
            $table->integer('banner_ttl')->default(600);       // 10 minutos
            $table->integer('config_ttl')->default(3600);      // 1 hora

            // Metadatos
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table->unique('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache_settings');
    }
};
