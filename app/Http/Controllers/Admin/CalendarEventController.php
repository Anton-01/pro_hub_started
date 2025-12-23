<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    private function getCompanyId(): string
    {
        return auth()->user()->company_id;
    }

    /**
     * Listar eventos
     */
    public function index(Request $request)
    {
        $query = CalendarEvent::where('company_id', $this->getCompanyId());

        if ($request->filled('search')) {
            $query->where('title', 'ilike', "%{$request->search}%");
        }

        if ($request->filled('month')) {
            $query->whereMonth('event_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('event_date', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->orderBy('event_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Guardar nuevo evento
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'event_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'is_all_day' => 'boolean',
            'is_recurring' => 'boolean',
            'recurrence_rule' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        CalendarEvent::create([
            'company_id' => $this->getCompanyId(),
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'event_date' => $validated['event_date'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'is_all_day' => $request->boolean('is_all_day'),
            'is_recurring' => $request->boolean('is_recurring'),
            'recurrence_rule' => $validated['recurrence_rule'] ?? null,
            'color' => $validated['color'] ?? '#3b82f6',
            'icon' => $validated['icon'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Evento creado correctamente.');
    }

    /**
     * Mostrar evento
     */
    public function show(CalendarEvent $event)
    {
        $this->authorizeAccess($event);

        return view('admin.events.show', compact('event'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(CalendarEvent $event)
    {
        $this->authorizeAccess($event);

        return view('admin.events.edit', compact('event'));
    }

    /**
     * Actualizar evento
     */
    public function update(Request $request, CalendarEvent $event)
    {
        $this->authorizeAccess($event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'event_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'is_all_day' => 'boolean',
            'is_recurring' => 'boolean',
            'recurrence_rule' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'event_date' => $validated['event_date'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'is_all_day' => $request->boolean('is_all_day'),
            'is_recurring' => $request->boolean('is_recurring'),
            'recurrence_rule' => $validated['recurrence_rule'] ?? null,
            'color' => $validated['color'] ?? '#3b82f6',
            'icon' => $validated['icon'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Evento actualizado correctamente.');
    }

    /**
     * Eliminar evento
     */
    public function destroy(CalendarEvent $event)
    {
        $this->authorizeAccess($event);

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Evento eliminado correctamente.');
    }

    /**
     * Cambiar estado
     */
    public function toggleStatus(CalendarEvent $event)
    {
        $this->authorizeAccess($event);

        $newStatus = $event->status === 'active' ? 'inactive' : 'active';
        $event->update(['status' => $newStatus]);

        return back()->with('success', 'Estado del evento actualizado.');
    }

    private function authorizeAccess(CalendarEvent $event): void
    {
        if ($event->company_id !== $this->getCompanyId()) {
            abort(403, 'No tienes acceso a este evento.');
        }
    }
}
