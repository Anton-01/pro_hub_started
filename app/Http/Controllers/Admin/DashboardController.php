<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Module;
use App\Models\Contact;
use App\Models\CalendarEvent;
use App\Models\News;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Mostrar dashboard principal
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        }

        return $this->adminDashboard($user);
    }

    /**
     * Dashboard para Super Admin
     */
    private function superAdminDashboard()
    {
        // Estadísticas globales
        $stats = [
            'total_companies' => Company::count(),
            'active_companies' => Company::where('status', 'active')->count(),
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_admins' => User::whereIn('role', ['admin', 'super_admin'])->count(),
            'total_modules' => Module::count(),
            'total_contacts' => Contact::count(),
            'total_events' => CalendarEvent::count(),
            'total_news' => News::count(),
        ];

        // Últimos registros
        $recentCompanies = Company::latest()->take(5)->get();
        $recentUsers = User::with('company')->latest()->take(5)->get();

        // Actividad reciente
        $recentActivity = ActivityLog::with(['user', 'company'])
            ->latest('created_at')
            ->take(10)
            ->get();

        // Empresas por estado
        $companiesByStatus = Company::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Usuarios registrados por mes (últimos 6 meses)
        $usersByMonth = User::selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, count(*) as count")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return view('admin.dashboard.super-admin', compact(
            'stats',
            'recentCompanies',
            'recentUsers',
            'recentActivity',
            'companiesByStatus',
            'usersByMonth'
        ));
    }

    /**
     * Dashboard para Admin de empresa
     */
    private function adminDashboard(User $user)
    {
        $companyId = $user->company_id;

        // Estadísticas de la empresa
        $stats = [
            'total_users' => User::where('company_id', $companyId)->count(),
            'active_users' => User::where('company_id', $companyId)->where('status', 'active')->count(),
            'total_modules' => Module::where('company_id', $companyId)->count(),
            'active_modules' => Module::where('company_id', $companyId)->where('status', 'active')->count(),
            'total_contacts' => Contact::where('company_id', $companyId)->count(),
            'upcoming_events' => CalendarEvent::where('company_id', $companyId)
                ->where('event_date', '>=', now())
                ->count(),
            'active_news' => News::where('company_id', $companyId)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                })
                ->count(),
        ];

        // Próximos eventos
        $upcomingEvents = CalendarEvent::where('company_id', $companyId)
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->take(5)
            ->get();

        // Últimos usuarios registrados
        $recentUsers = User::where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get();

        // Actividad reciente de la empresa
        $recentActivity = ActivityLog::where('company_id', $companyId)
            ->with('user')
            ->latest('created_at')
            ->take(10)
            ->get();

        // Noticias activas
        $activeNews = News::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('is_priority', 'desc')
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        return view('admin.dashboard.admin', compact(
            'stats',
            'upcomingEvents',
            'recentUsers',
            'recentActivity',
            'activeNews'
        ));
    }
}
