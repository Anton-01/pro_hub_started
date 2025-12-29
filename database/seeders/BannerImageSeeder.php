<?php

namespace Database\Seeders;

use App\Models\BannerImage;
use Illuminate\Database\Seeder;

class BannerImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoCompanyId = 'bfbcb072-61ba-49bf-bc14-aeaace3778e4';

        $banners = [
            [
                'title' => 'Bienvenidos al Portal',
                'description' => 'Tu centro de información y comunicación empresarial',
                'image_path' => 'banners/demo/welcome-banner.jpg',
                'link_url' => null,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Día de Integración',
                'description' => 'Próximo viernes - ¡No te lo pierdas!',
                'image_path' => 'banners/demo/integration-day.jpg',
                'link_url' => '/eventos/integracion',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Nuevos Beneficios',
                'description' => 'Conoce las nuevas prestaciones disponibles para ti',
                'image_path' => 'banners/demo/benefits.jpg',
                'link_url' => '/beneficios',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Capacitación Continua',
                'description' => 'Desarrolla tu potencial con nuestros programas de formación',
                'image_path' => 'banners/demo/training.jpg',
                'link_url' => '/capacitacion',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Seguridad Primero',
                'description' => 'Recuerda seguir los protocolos de seguridad',
                'image_path' => 'banners/demo/safety.jpg',
                'link_url' => null,
                'order' => 5,
                'is_active' => false,
            ],
        ];

        foreach ($banners as $bannerData) {
            BannerImage::create(array_merge($bannerData, [
                'company_id' => $demoCompanyId,
            ]));
        }

        $this->command->info('Imágenes de banner creadas exitosamente.');
    }
}
