<?php

namespace Database\Seeders;

use App\Models\CalendarEvent;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CalendarEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoCompanyId = 'c0000000-0000-0000-0000-000000000001';
        $currentMonth = Carbon::now();

        $events = [
            [
                'title' => 'Reunión de Inicio de Mes',
                'description' => 'Reunión mensual con todos los equipos',
                'content' => 'Revisión de objetivos del mes anterior y planificación del nuevo mes.',
                'event_date' => $currentMonth->copy()->startOfMonth()->addDays(2),
                'start_time' => '09:00:00',
                'end_time' => '10:30:00',
                'color' => '#3b82f6',
            ],
            [
                'title' => 'Día de Pago',
                'description' => 'Depósito de nómina quincenal',
                'event_date' => $currentMonth->copy()->startOfMonth()->addDays(14),
                'is_all_day' => true,
                'color' => '#10b981',
            ],
            [
                'title' => 'Capacitación de Seguridad',
                'description' => 'Capacitación obligatoria de seguridad e higiene',
                'content' => 'Todos los empleados deben asistir a esta capacitación.',
                'event_date' => $currentMonth->copy()->startOfMonth()->addDays(10),
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'color' => '#f59e0b',
            ],
            [
                'title' => 'Cierre de Mes',
                'description' => 'Fecha límite para entrega de reportes',
                'event_date' => $currentMonth->copy()->endOfMonth(),
                'is_all_day' => true,
                'color' => '#ef4444',
            ],
            [
                'title' => 'Evento de Integración',
                'description' => 'Actividad de integración de equipos',
                'content' => 'Actividades recreativas y convivio para fortalecer el trabajo en equipo.',
                'event_date' => $currentMonth->copy()->startOfMonth()->addDays(20),
                'start_time' => '18:00:00',
                'end_time' => '21:00:00',
                'color' => '#8b5cf6',
            ],
        ];

        foreach ($events as $eventData) {
            CalendarEvent::create(array_merge($eventData, [
                'company_id' => $demoCompanyId,
                'status' => 'active',
            ]));
        }

        $this->command->info('Eventos de calendario creados exitosamente.');
    }
}
