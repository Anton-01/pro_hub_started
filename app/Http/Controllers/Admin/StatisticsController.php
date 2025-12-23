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
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Dashboard de estadísticas (solo Super Admin)
     */
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'total_companies' => Company::count(),
            'active_companies' => Company::where('status', 'active')->count(),
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'super_admins' => User::where('role', 'super_admin')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'regular_users' => User::where('role', 'user')->count(),
        ];

        // Empresas más activas (por número de usuarios)
        $topCompanies = Company::withCount('users')
            ->orderBy('users_count', 'desc')
            ->take(10)
            ->get();

        // Registros por mes (últimos 12 meses)
        $registrationsByMonth = User::selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, count(*) as count")
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Actividad por día (últimos 30 días)
        $activityByDay = ActivityLog::selectRaw("DATE(created_at) as date, count(*) as count")
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return view('admin.statistics.index', compact('stats', 'topCompanies', 'registrationsByMonth', 'activityByDay'));
    }

    /**
     * Estadísticas de empresas
     */
    public function companies(Request $request)
    {
        // Empresas con conteos
        $companies = Company::withCount(['users', 'modules', 'contacts', 'news', 'calendarEvents'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Distribución por estado
        $byStatus = Company::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Empresas creadas por mes
        $byMonth = Company::selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, count(*) as count")
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return view('admin.statistics.companies', compact('companies', 'byStatus', 'byMonth'));
    }

    /**
     * Estadísticas de usuarios
     */
    public function users(Request $request)
    {
        // Distribución por rol
        $byRole = User::selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        // Distribución por estado
        $byStatus = User::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Usuarios por empresa
        $byCompany = Company::withCount('users')
            ->orderBy('users_count', 'desc')
            ->take(20)
            ->get();

        // Usuarios activos recientemente (últimos 7 días)
        $recentlyActive = User::whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subDays(7))
            ->count();

        // Registros por mes
        $byMonth = User::selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, count(*) as count")
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return view('admin.statistics.users', compact('byRole', 'byStatus', 'byCompany', 'recentlyActive', 'byMonth'));
    }

    /**
     * Estadísticas de actividad
     */
    public function activity(Request $request)
    {
        // Actividad por tipo de acción
        $byAction = ActivityLog::selectRaw('action, count(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->pluck('count', 'action')
            ->toArray();

        // Actividad por tipo de entidad
        $byEntity = ActivityLog::selectRaw('entity_type, count(*) as count')
            ->groupBy('entity_type')
            ->orderBy('count', 'desc')
            ->pluck('count', 'entity_type')
            ->toArray();

        // Usuarios más activos
        $topUsers = User::withCount('activityLogs')
            ->orderBy('activity_logs_count', 'desc')
            ->take(10)
            ->get();

        // Actividad por hora del día
        $byHour = ActivityLog::selectRaw("EXTRACT(HOUR FROM created_at)::integer as hour, count(*) as count")
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        // Actividad por día de la semana
        $byDayOfWeek = ActivityLog::selectRaw("EXTRACT(DOW FROM created_at)::integer as dow, count(*) as count")
            ->groupBy('dow')
            ->orderBy('dow')
            ->pluck('count', 'dow')
            ->toArray();

        return view('admin.statistics.activity', compact('byAction', 'byEntity', 'topUsers', 'byHour', 'byDayOfWeek'));
    }

    /**
     * Exportar estadísticas
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'general');

        $data = match ($type) {
            'companies' => Company::withCount(['users', 'modules', 'contacts', 'news', 'calendarEvents'])->get(),
            'users' => User::with('company:id,name')->get(),
            'activity' => ActivityLog::with(['user:id,name,email', 'company:id,name'])
                ->where('created_at', '>=', now()->subDays(30))
                ->get(),
            default => [
                'companies' => Company::count(),
                'users' => User::count(),
                'modules' => Module::count(),
                'contacts' => Contact::count(),
                'events' => CalendarEvent::count(),
                'news' => News::count(),
            ],
        };

        return response()->json($data);
    }
}
