<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Company;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Listar logs de actividad (solo Super Admin)
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'company']);

        // Filtros
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        // Datos para filtros
        $companies = Company::orderBy('name')->get();
        $actions = ActivityLog::distinct()->pluck('action');
        $entityTypes = ActivityLog::distinct()->pluck('entity_type');

        return view('admin.activity-logs.index', compact('logs', 'companies', 'actions', 'entityTypes'));
    }

    /**
     * Mostrar detalle de un log
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load(['user', 'company']);

        return view('admin.activity-logs.show', compact('activityLog'));
    }
}
