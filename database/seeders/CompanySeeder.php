<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyConfiguration;
use App\Models\CacheSetting;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Empresa del Sistema (para Super Admins)
        $systemCompany = Company::create([
            'id' => 'c0000000-0000-0000-0000-000000000000',
            'name' => 'Panel Empresarial - Sistema',
            'slug' => 'sistema',
            'email' => 'sistema@panelempresarial.com',
            'status' => 'active',
            'max_admins' => 2,
        ]);

        CompanyConfiguration::create([
            'company_id' => $systemCompany->id,
            'primary_color' => '#4c78dd',
            'secondary_color' => '#1a1f2c',
        ]);

        CacheSetting::create([
            'company_id' => $systemCompany->id,
        ]);

        // Empresa de demostración principal
        $demoCompany = Company::create([
            'id' => 'c0000000-0000-0000-0000-000000000001',
            'name' => 'Empresa Demo',
            'slug' => 'empresa-demo',
            'tax_id' => 'XAXX010101000',
            'email' => 'contacto@empresademo.com',
            'phone' => '+52 55 1234 5678',
            'address' => 'Av. Reforma 123, Col. Centro, CDMX',
            'website' => 'https://empresademo.com',
            'status' => 'active',
            'max_admins' => 5,
        ]);

        CompanyConfiguration::create([
            'company_id' => $demoCompany->id,
            'logo_url' => null,
            'primary_color' => '#c9a227',
            'secondary_color' => '#0a1744',
            'accent_color' => '#f59e0b',
            'background_color' => '#0d1b4c',
            'text_color' => '#ffffff',
            'header_text' => 'Portal de Empleados',
            'footer_text' => '© 2024 Empresa Demo. Todos los derechos reservados.',
            'show_calendar' => true,
            'show_news_ticker' => true,
            'show_contacts' => true,
            'meta_title' => 'Empresa Demo - Portal de Empleados',
            'meta_description' => 'Portal interno de empleados de Empresa Demo',
        ]);

        CacheSetting::create([
            'company_id' => $demoCompany->id,
        ]);

        // Segunda empresa de prueba
        $techCompany = Company::create([
            'id' => 'c0000000-0000-0000-0000-000000000002',
            'name' => 'Tech Solutions SA',
            'slug' => 'tech-solutions',
            'tax_id' => 'TSO120101ABC',
            'email' => 'info@techsolutions.mx',
            'phone' => '+52 55 9876 5432',
            'address' => 'Av. Insurgentes Sur 456, Del Valle, CDMX',
            'website' => 'https://techsolutions.mx',
            'status' => 'active',
            'max_admins' => 5,
        ]);

        CompanyConfiguration::create([
            'company_id' => $techCompany->id,
            'primary_color' => '#3b82f6',
            'secondary_color' => '#1e3a8a',
            'accent_color' => '#10b981',
            'background_color' => '#111827',
            'text_color' => '#f9fafb',
            'header_text' => 'Tech Solutions - Intranet',
            'footer_text' => '© 2024 Tech Solutions SA',
        ]);

        CacheSetting::create([
            'company_id' => $techCompany->id,
        ]);

        $this->command->info('✓ Empresas creadas:');
        $this->command->info('  - Panel Empresarial - Sistema (para Super Admins)');
        $this->command->info('  - Empresa Demo');
        $this->command->info('  - Tech Solutions SA');
    }
}
