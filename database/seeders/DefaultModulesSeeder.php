<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultModules = [
            // ========================================
            // ERPs Empresariales
            // ========================================
            [
                'category' => 'erp',
                'system_name' => 'SAP',
                'label' => 'SAP ERP',
                'description' => 'Sistema de Planificación de Recursos Empresariales',
                'type' => 'external',
                'url' => 'https://www.sap.com',
                'target' => '_blank',
                'icon' => 'fas fa-building',
                'icon_type' => 'class',
                'background_color' => '#0070C0',
                'is_featured' => true,
                'group_name' => 'ERP',
                'sort_order' => 1,
            ],
            [
                'category' => 'erp',
                'system_name' => 'Odoo',
                'label' => 'Odoo',
                'description' => 'Suite de aplicaciones empresariales de código abierto',
                'type' => 'external',
                'url' => 'https://www.odoo.com',
                'target' => '_blank',
                'icon' => 'fas fa-cogs',
                'icon_type' => 'class',
                'background_color' => '#714B67',
                'is_featured' => true,
                'group_name' => 'ERP',
                'sort_order' => 2,
            ],
            [
                'category' => 'erp',
                'system_name' => 'Microsoft Dynamics 365',
                'label' => 'Microsoft Dynamics 365',
                'description' => 'Aplicaciones empresariales inteligentes de Microsoft',
                'type' => 'external',
                'url' => 'https://dynamics.microsoft.com',
                'target' => '_blank',
                'icon' => 'fab fa-microsoft',
                'icon_type' => 'class',
                'background_color' => '#00A4EF',
                'is_featured' => true,
                'group_name' => 'ERP',
                'sort_order' => 3,
            ],
            [
                'category' => 'erp',
                'system_name' => 'Oracle NetSuite',
                'label' => 'Oracle NetSuite',
                'description' => 'ERP en la nube de Oracle',
                'type' => 'external',
                'url' => 'https://www.netsuite.com',
                'target' => '_blank',
                'icon' => 'fas fa-database',
                'icon_type' => 'class',
                'background_color' => '#E10000',
                'is_featured' => false,
                'group_name' => 'ERP',
                'sort_order' => 4,
            ],

            // ========================================
            // CRM - Gestión de Relaciones con Clientes
            // ========================================
            [
                'category' => 'crm',
                'system_name' => 'Salesforce',
                'label' => 'Salesforce CRM',
                'description' => 'Plataforma líder de CRM',
                'type' => 'external',
                'url' => 'https://www.salesforce.com',
                'target' => '_blank',
                'icon' => 'fab fa-salesforce',
                'icon_type' => 'class',
                'background_color' => '#00A1E0',
                'is_featured' => true,
                'group_name' => 'CRM',
                'sort_order' => 10,
            ],
            [
                'category' => 'crm',
                'system_name' => 'HubSpot',
                'label' => 'HubSpot CRM',
                'description' => 'CRM gratuito con herramientas de marketing',
                'type' => 'external',
                'url' => 'https://www.hubspot.com',
                'target' => '_blank',
                'icon' => 'fas fa-chart-line',
                'icon_type' => 'class',
                'background_color' => '#FF7A59',
                'is_featured' => true,
                'group_name' => 'CRM',
                'sort_order' => 11,
            ],
            [
                'category' => 'crm',
                'system_name' => 'Zoho CRM',
                'label' => 'Zoho CRM',
                'description' => 'CRM todo en uno para empresas',
                'type' => 'external',
                'url' => 'https://www.zoho.com/crm',
                'target' => '_blank',
                'icon' => 'fas fa-users',
                'icon_type' => 'class',
                'background_color' => '#E42527',
                'is_featured' => false,
                'group_name' => 'CRM',
                'sort_order' => 12,
            ],

            // ========================================
            // Recursos Humanos
            // ========================================
            [
                'category' => 'hr',
                'system_name' => 'Workday',
                'label' => 'Workday HCM',
                'description' => 'Sistema de gestión de capital humano',
                'type' => 'external',
                'url' => 'https://www.workday.com',
                'target' => '_blank',
                'icon' => 'fas fa-user-tie',
                'icon_type' => 'class',
                'background_color' => '#E55939',
                'is_featured' => false,
                'group_name' => 'RRHH',
                'sort_order' => 20,
            ],
            [
                'category' => 'hr',
                'system_name' => 'BambooHR',
                'label' => 'BambooHR',
                'description' => 'Software de recursos humanos para PyMEs',
                'type' => 'external',
                'url' => 'https://www.bamboohr.com',
                'target' => '_blank',
                'icon' => 'fas fa-leaf',
                'icon_type' => 'class',
                'background_color' => '#73C41D',
                'is_featured' => false,
                'group_name' => 'RRHH',
                'sort_order' => 21,
            ],

            // ========================================
            // Contabilidad y Finanzas
            // ========================================
            [
                'category' => 'accounting',
                'system_name' => 'QuickBooks',
                'label' => 'QuickBooks',
                'description' => 'Software de contabilidad para negocios',
                'type' => 'external',
                'url' => 'https://quickbooks.intuit.com',
                'target' => '_blank',
                'icon' => 'fas fa-calculator',
                'icon_type' => 'class',
                'background_color' => '#2CA01C',
                'is_featured' => false,
                'group_name' => 'Finanzas',
                'sort_order' => 30,
            ],
            [
                'category' => 'accounting',
                'system_name' => 'Xero',
                'label' => 'Xero',
                'description' => 'Contabilidad en línea para pequeñas empresas',
                'type' => 'external',
                'url' => 'https://www.xero.com',
                'target' => '_blank',
                'icon' => 'fas fa-coins',
                'icon_type' => 'class',
                'background_color' => '#13B5EA',
                'is_featured' => false,
                'group_name' => 'Finanzas',
                'sort_order' => 31,
            ],

            // ========================================
            // Gestión de Proyectos
            // ========================================
            [
                'category' => 'project_management',
                'system_name' => 'Jira',
                'label' => 'Jira',
                'description' => 'Gestión de proyectos ágiles',
                'type' => 'external',
                'url' => 'https://www.atlassian.com/software/jira',
                'target' => '_blank',
                'icon' => 'fab fa-jira',
                'icon_type' => 'class',
                'background_color' => '#0052CC',
                'is_featured' => true,
                'group_name' => 'Proyectos',
                'sort_order' => 40,
            ],
            [
                'category' => 'project_management',
                'system_name' => 'Asana',
                'label' => 'Asana',
                'description' => 'Gestión de trabajo y proyectos',
                'type' => 'external',
                'url' => 'https://www.asana.com',
                'target' => '_blank',
                'icon' => 'fas fa-tasks',
                'icon_type' => 'class',
                'background_color' => '#F06A6A',
                'is_featured' => false,
                'group_name' => 'Proyectos',
                'sort_order' => 41,
            ],
            [
                'category' => 'project_management',
                'system_name' => 'Monday',
                'label' => 'Monday.com',
                'description' => 'Plataforma de trabajo colaborativo',
                'type' => 'external',
                'url' => 'https://monday.com',
                'target' => '_blank',
                'icon' => 'fas fa-calendar-alt',
                'icon_type' => 'class',
                'background_color' => '#FF3D57',
                'is_featured' => false,
                'group_name' => 'Proyectos',
                'sort_order' => 42,
            ],

            // ========================================
            // Comunicación
            // ========================================
            [
                'category' => 'communication',
                'system_name' => 'Slack',
                'label' => 'Slack',
                'description' => 'Mensajería y colaboración empresarial',
                'type' => 'external',
                'url' => 'https://slack.com',
                'target' => '_blank',
                'icon' => 'fab fa-slack',
                'icon_type' => 'class',
                'background_color' => '#4A154B',
                'is_featured' => true,
                'group_name' => 'Comunicación',
                'sort_order' => 50,
            ],
            [
                'category' => 'communication',
                'system_name' => 'Microsoft Teams',
                'label' => 'Microsoft Teams',
                'description' => 'Plataforma de colaboración de Microsoft',
                'type' => 'external',
                'url' => 'https://teams.microsoft.com',
                'target' => '_blank',
                'icon' => 'fab fa-microsoft',
                'icon_type' => 'class',
                'background_color' => '#505AC9',
                'is_featured' => true,
                'group_name' => 'Comunicación',
                'sort_order' => 51,
            ],
        ];

        foreach ($defaultModules as $module) {
            \App\Models\DefaultModule::create($module);
        }
    }
}
