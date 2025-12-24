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
        $systemCompanyId = 'c0000000-0000-0000-0000-000000000000';
        $demoCompanyId = 'c0000000-0000-0000-0000-000000000001';
        $techCompanyId = 'c0000000-0000-0000-0000-000000000002';

        // =============================================
        // SUPER ADMINS (máximo 2 según requerimiento)
        // =============================================

        User::create([
            'id' => 'u0000000-0000-0000-0000-000000000001',
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
            'id' => 'u0000000-0000-0000-0000-000000000002',
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
            'id' => 'u0000000-0000-0000-0000-000000000010',
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
            'id' => 'u0000000-0000-0000-0000-000000000011',
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
            'id' => 'u0000000-0000-0000-0000-000000000012',
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
            'id' => 'u0000000-0000-0000-0000-000000000013',
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
            'id' => 'u0000000-0000-0000-0000-000000000020',
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
            'id' => 'u0000000-0000-0000-0000-000000000021',
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
