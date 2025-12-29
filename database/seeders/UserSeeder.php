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
        $systemCompanyId = 'c775596e-6fae-4f78-8a48-6520d9485945';
        $demoCompanyId = 'bfbcb072-61ba-49bf-bc14-aeaace3778e4';
        $techCompanyId = '3f3f585f-ee7a-44e3-9a77-99600ab30d69';

        // =============================================
        // SUPER ADMINS (máximo 2 según requerimiento)
        // =============================================

        User::create([
            'id' => 'dcf7d7df-62b8-4359-9d06-646d82a4e2ae',
            'company_id' => $systemCompanyId,
            'email' => 'superadmin@panelempresarial.com',
            'password' => Hash::make('SuperAdmin123!'),
            'name' => 'Super',
            'last_name' => 'Administrador',
            'phone' => '+52 55 0000 0001',
            'role' => 'super_admin',
            'status' => 'active',
            'is_primary_admin' => false, // Super admins no necesitan este flag
            'email_verified_at' => now(),
        ]);

        User::create([
            'id' => '4b9e037f-116d-4507-9385-b09485a71fd3',
            'company_id' => $systemCompanyId,
            'email' => 'admin.sistema@panelempresarial.com',
            'password' => Hash::make('SuperAdmin123!'),
            'name' => 'Administrador',
            'last_name' => 'del Sistema',
            'phone' => '+52 55 0000 0002',
            'role' => 'super_admin',
            'status' => 'active',
            'is_primary_admin' => false,
            'email_verified_at' => now(),
        ]);

        // =============================================
        // EMPRESA DEMO - Usuarios
        // =============================================

        // Admin Primario de Empresa Demo (puede crear otros admins)
        User::create([
            'id' => '24bad944-81a7-47b2-b0a8-5163a157f3d2',
            'company_id' => $demoCompanyId,
            'email' => 'admin@empresademo.com',
            'password' => Hash::make('Admin123!'),
            'name' => 'María',
            'last_name' => 'López Rodríguez',
            'phone' => '+52 55 1111 0001',
            'role' => 'admin',
            'status' => 'active',
            'is_primary_admin' => true, // Admin primario
            'email_verified_at' => now(),
        ]);

        // Admin Secundario de Empresa Demo
        User::create([
            'id' => '2c9eccd4-a4c4-4479-9ff4-eb307b1536f1',
            'company_id' => $demoCompanyId,
            'email' => 'admin2@empresademo.com',
            'password' => Hash::make('Admin123!'),
            'name' => 'Carlos',
            'last_name' => 'García Mendoza',
            'phone' => '+52 55 1111 0002',
            'role' => 'admin',
            'status' => 'active',
            'is_primary_admin' => false, // Admin secundario
            'email_verified_at' => now(),
        ]);

        // Usuarios normales de Empresa Demo
        User::create([
            'id' => '00fb2daf-9981-42ec-922d-84371d52be62',
            'company_id' => $demoCompanyId,
            'email' => 'juan.hernandez@empresademo.com',
            'password' => Hash::make('Usuario123!'),
            'name' => 'Juan',
            'last_name' => 'Hernández Pérez',
            'phone' => '+52 55 1111 0003',
            'role' => 'user',
            'status' => 'active',
            'is_primary_admin' => false,
            'email_verified_at' => now(),
        ]);

        User::create([
            'id' => '933a1c02-1cac-4aea-bad9-ae9183a9797a',
            'company_id' => $demoCompanyId,
            'email' => 'ana.martinez@empresademo.com',
            'password' => Hash::make('Usuario123!'),
            'name' => 'Ana',
            'last_name' => 'Martínez Sánchez',
            'phone' => '+52 55 1111 0004',
            'role' => 'user',
            'status' => 'active',
            'is_primary_admin' => false,
            'email_verified_at' => now(),
        ]);

        // =============================================
        // TECH SOLUTIONS - Usuarios
        // =============================================

        // Admin Primario de Tech Solutions
        User::create([
            'id' => 'fd7de8c6-bdb5-4e04-bb64-10d0b99a44b5',
            'company_id' => $techCompanyId,
            'email' => 'admin@techsolutions.mx',
            'password' => Hash::make('Admin123!'),
            'name' => 'Roberto',
            'last_name' => 'Martínez Silva',
            'phone' => '+52 55 2222 0001',
            'role' => 'admin',
            'status' => 'active',
            'is_primary_admin' => true,
            'email_verified_at' => now(),
        ]);

        // Usuario de Tech Solutions
        User::create([
            'id' => '5a86323b-430a-494b-a050-15effcfe764e',
            'company_id' => $techCompanyId,
            'email' => 'developer@techsolutions.mx',
            'password' => Hash::make('Usuario123!'),
            'name' => 'Luis',
            'last_name' => 'Ramírez Torres',
            'phone' => '+52 55 2222 0002',
            'role' => 'user',
            'status' => 'active',
            'is_primary_admin' => false,
            'email_verified_at' => now(),
        ]);

        $this->command->newLine();
        $this->command->info('✓ Usuarios creados exitosamente');
        $this->command->newLine();
        $this->command->warn('╔════════════════════════════════════════════════════════════╗');
        $this->command->warn('║              CREDENCIALES DE ACCESO                        ║');
        $this->command->warn('╠════════════════════════════════════════════════════════════╣');
        $this->command->warn('║ SUPER ADMINS (acceso total):                               ║');
        $this->command->warn('║   superadmin@panelempresarial.com     / SuperAdmin123!     ║');
        $this->command->warn('║   admin.sistema@panelempresarial.com  / SuperAdmin123!     ║');
        $this->command->warn('╠════════════════════════════════════════════════════════════╣');
        $this->command->warn('║ EMPRESA DEMO:                                              ║');
        $this->command->warn('║   admin@empresademo.com (primario)    / Admin123!          ║');
        $this->command->warn('║   admin2@empresademo.com              / Admin123!          ║');
        $this->command->warn('╠════════════════════════════════════════════════════════════╣');
        $this->command->warn('║ TECH SOLUTIONS:                                            ║');
        $this->command->warn('║   admin@techsolutions.mx (primario)   / Admin123!          ║');
        $this->command->warn('╚════════════════════════════════════════════════════════════╝');
        $this->command->newLine();
    }
}
