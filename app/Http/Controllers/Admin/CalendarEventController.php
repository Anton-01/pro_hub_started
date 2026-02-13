<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalendarEventRequest;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Mckenziearts\Notify\Exceptions\InvalidNotificationException;

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
    public function store(CalendarEventRequest $request)
    {
        $validated = $request->validated();

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
    public function update(CalendarEventRequest $request, CalendarEvent $event)
    {
        $this->authorizeAccess($event);

        $validated = $request->validated();

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
     * @throws InvalidNotificationException
     */
    public function toggleStatus(Request $request, CalendarEvent $event)
    {
        $this->authorizeAccess($event);

        $newStatus = $event->status === 'active' ? 'inactive' : 'active';
        $event->update(['status' => $newStatus]);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Estado actualizado correctamente'
            ]);
        }

        notify()->success()->message('Estado del evento actualizado.')->send();
        return back();
    }

    private function authorizeAccess(CalendarEvent $event): void
    {
        if ($event->company_id !== $this->getCompanyId()) {
            abort(403, 'No tienes acceso a este evento.');
        }
    }
}
