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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('company_id');

            // Datos del evento
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->text('content')->nullable();

            // Fecha y hora
            $table->date('event_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_all_day')->default(false);

            // Recurrencia
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_rule', 255)->nullable(); // Formato iCal RRULE

            // Apariencia
            $table->string('color', 7)->default('#c9a227');
            $table->string('icon', 100)->nullable();

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
            $table->index('event_date');
            $table->index('deleted_at');
        });

        // Agregar columna status con tipo PostgreSQL nativo
        DB::statement("ALTER TABLE calendar_events ADD COLUMN status record_status DEFAULT 'active'");
        DB::statement("CREATE INDEX idx_calendar_events_status ON calendar_events(status)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
