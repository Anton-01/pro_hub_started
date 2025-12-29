<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoCompanyId = 'bfbcb072-61ba-49bf-bc14-aeaace3778e4';

        $contacts = [
            [
                'name' => 'Recursos Humanos',
                'position' => 'Departamento',
                'department' => 'Recursos Humanos',
                'email' => 'rh@empresa-demo.com',
                'phone' => '+52 555 100 2001',
                'extension' => '2001',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'María García López',
                'position' => 'Directora de Recursos Humanos',
                'department' => 'Recursos Humanos',
                'email' => 'maria.garcia@empresa-demo.com',
                'phone' => '+52 555 100 2002',
                'extension' => '2002',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Sistemas e Informática',
                'position' => 'Departamento',
                'department' => 'Tecnología',
                'email' => 'sistemas@empresa-demo.com',
                'phone' => '+52 555 100 3001',
                'extension' => '3001',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'Carlos Rodríguez Martínez',
                'position' => 'Director de Tecnología',
                'department' => 'Tecnología',
                'email' => 'carlos.rodriguez@empresa-demo.com',
                'phone' => '+52 555 100 3002',
                'extension' => '3002',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'name' => 'Soporte Técnico',
                'position' => 'Mesa de Ayuda',
                'department' => 'Tecnología',
                'email' => 'soporte@empresa-demo.com',
                'phone' => '+52 555 100 3003',
                'extension' => '3003',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'name' => 'Finanzas',
                'position' => 'Departamento',
                'department' => 'Finanzas',
                'email' => 'finanzas@empresa-demo.com',
                'phone' => '+52 555 100 4001',
                'extension' => '4001',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'name' => 'Ana Martínez Pérez',
                'position' => 'Directora Financiera',
                'department' => 'Finanzas',
                'email' => 'ana.martinez@empresa-demo.com',
                'phone' => '+52 555 100 4002',
                'extension' => '4002',
                'is_active' => true,
                'order' => 7,
            ],
            [
                'name' => 'Recepción',
                'position' => 'Atención General',
                'department' => 'Administración',
                'email' => 'recepcion@empresa-demo.com',
                'phone' => '+52 555 100 1001',
                'extension' => '1001',
                'is_active' => true,
                'order' => 8,
            ],
            [
                'name' => 'Seguridad',
                'position' => 'Departamento',
                'department' => 'Operaciones',
                'email' => 'seguridad@empresa-demo.com',
                'phone' => '+52 555 100 5001',
                'extension' => '5001',
                'is_active' => true,
                'order' => 9,
            ],
            [
                'name' => 'Emergencias',
                'position' => 'Línea Directa',
                'department' => 'Operaciones',
                'email' => 'emergencias@empresa-demo.com',
                'phone' => '+52 555 100 9999',
                'extension' => '9999',
                'is_active' => true,
                'order' => 10,
            ],
        ];

        foreach ($contacts as $contactData) {
            Contact::create(array_merge($contactData, [
                'company_id' => $demoCompanyId,
            ]));
        }

        $this->command->info('Contactos creados exitosamente.');
    }
}
