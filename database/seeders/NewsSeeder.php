<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoCompanyId = 'c0000000-0000-0000-0000-000000000001';
        $now = Carbon::now();

        $newsItems = [
            [
                'title' => 'Nuevo Sistema de Comunicación',
                'content' => 'Estamos emocionados de presentar nuestro nuevo sistema de comunicación interna. Este portal centraliza toda la información importante de la empresa.',
                'priority' => 1,
                'is_active' => true,
                'published_at' => $now->copy()->subDays(2),
                'expires_at' => $now->copy()->addMonths(1),
            ],
            [
                'title' => 'Actualización de Políticas de Seguridad',
                'content' => 'Se han actualizado las políticas de seguridad informática. Por favor revisa el documento completo en el portal de recursos humanos.',
                'priority' => 2,
                'is_active' => true,
                'published_at' => $now->copy()->subDay(),
                'expires_at' => $now->copy()->addWeeks(2),
            ],
            [
                'title' => 'Día de Integración - Próximo Viernes',
                'content' => 'Los invitamos al evento de integración que se llevará a cabo el próximo viernes. Habrá actividades recreativas y convivio.',
                'priority' => 3,
                'is_active' => true,
                'published_at' => $now,
                'expires_at' => $now->copy()->addWeek(),
            ],
            [
                'title' => 'Mantenimiento Programado',
                'content' => 'El sistema estará en mantenimiento este domingo de 2:00 AM a 6:00 AM. Disculpe las molestias.',
                'priority' => 1,
                'is_active' => true,
                'published_at' => $now,
                'expires_at' => $now->copy()->addDays(3),
            ],
            [
                'title' => 'Nuevas Prestaciones Disponibles',
                'content' => 'A partir del próximo mes, contaremos con nuevos beneficios para todos los colaboradores. Consulta los detalles con Recursos Humanos.',
                'priority' => 4,
                'is_active' => true,
                'published_at' => $now->copy()->subDays(5),
                'expires_at' => $now->copy()->addMonths(2),
            ],
            [
                'title' => 'Recordatorio: Evaluaciones de Desempeño',
                'content' => 'Les recordamos que las evaluaciones de desempeño del primer trimestre deben completarse antes del día 15 de este mes.',
                'priority' => 2,
                'is_active' => true,
                'published_at' => $now,
                'expires_at' => $now->copy()->addDays(15),
            ],
        ];

        foreach ($newsItems as $newsData) {
            News::create(array_merge($newsData, [
                'company_id' => $demoCompanyId,
            ]));
        }

        $this->command->info('Noticias creadas exitosamente.');
    }
}
