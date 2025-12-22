<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoCompanyId = 'c0000000-0000-0000-0000-000000000001';
        $techCompanyId = 'c0000000-0000-0000-0000-000000000002';

        // Super Admin de Empresa Demo
        User::create([
            'company_id' => $demoCompanyId,
            'email' => 'superadmin@empresademo.com',
            'password' => Hash::make('password123'),
            'name' => 'Carlos',
            'last_name' => 'García Mendoza',
            'phone' => '+52 55 1111 2222',
            'role' => 'super_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Admin de Empresa Demo
        User::create([
            'company_id' => $demoCompanyId,
            'email' => 'admin@empresademo.com',
            'password' => Hash::make('password123'),
            'name' => 'María',
            'last_name' => 'López Rodríguez',
            'phone' => '+52 55 3333 4444',
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Usuario normal de Empresa Demo
        User::create([
            'company_id' => $demoCompanyId,
            'email' => 'usuario@empresademo.com',
            'password' => Hash::make('password123'),
            'name' => 'Juan',
            'last_name' => 'Hernández Pérez',
            'phone' => '+52 55 5555 6666',
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Super Admin de Tech Solutions
        User::create([
            'company_id' => $techCompanyId,
            'email' => 'admin@techsolutions.mx',
            'password' => Hash::make('password123'),
            'name' => 'Roberto',
            'last_name' => 'Martínez Silva',
            'phone' => '+52 55 7777 8888',
            'role' => 'super_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Usuarios de prueba creados exitosamente.');
        $this->command->info('Credenciales de acceso:');
        $this->command->info('  - superadmin@empresademo.com / password123');
        $this->command->info('  - admin@empresademo.com / password123');
        $this->command->info('  - usuario@empresademo.com / password123');
    }
}
