<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DefaultModule;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModuleController extends Controller
{
    /**
     * Obtener el company_id del usuario actual
     */
    private function getCompanyId(): string
    {
        return auth()->user()->company_id;
    }

    /**
     * Listar módulos
     */
    public function index(Request $request)
    {
        $query = Module::where('company_id', $this->getCompanyId());

        if ($request->filled('search')) {
            $query->where('label', 'ilike', "%{$request->search}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $modules = $query->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.modules.index', compact('modules'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.modules.create');
    }

    /**
     * Guardar nuevo módulo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:link,modal,external',
            'url' => 'required|string|max:500',
            'target' => 'nullable|in:_self,_blank',
            'icon' => 'nullable|string|max:100',
            'highlight' => 'boolean',
            'background_color' => 'nullable|string|max:20',
            'group_name' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        // Obtener el último sort_order
        $lastOrder = Module::where('company_id', $this->getCompanyId())
            ->max('sort_order') ?? 0;

        Module::create([
            'company_id' => $this->getCompanyId(),
            'label' => $validated['label'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'url' => $validated['url'],
            'target' => $validated['target'] ?? '_self',
            'icon' => $validated['icon'] ?? null,
            'highlight' => $request->boolean('highlight'),
            'background_color' => $validated['background_color'] ?? null,
            'group_name' => $validated['group_name'] ?? null,
            'sort_order' => $lastOrder + 1,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Módulo creado correctamente.');
    }

    /**
     * Mostrar detalle del módulo
     */
    public function show(Module $module)
    {
        $this->authorizeAccess($module);

        return view('admin.modules.show', compact('module'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Module $module)
    {
        $this->authorizeAccess($module);

        return view('admin.modules.edit', compact('module'));
    }

    /**
     * Actualizar módulo
     */
    public function update(Request $request, Module $module)
    {
        $this->authorizeAccess($module);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:link,modal,external',
            'url' => 'required|string|max:500',
            'target' => 'nullable|in:_self,_blank',
            'icon' => 'nullable|string|max:100',
            'highlight' => 'boolean',
            'background_color' => 'nullable|string|max:20',
            'group_name' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $module->update([
            'label' => $validated['label'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'url' => $validated['url'],
            'target' => $validated['target'] ?? '_self',
            'icon' => $validated['icon'] ?? null,
            'highlight' => $request->boolean('highlight'),
            'background_color' => $validated['background_color'] ?? null,
            'group_name' => $validated['group_name'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Módulo actualizado correctamente.');
    }

    /**
     * Eliminar módulo
     */
    public function destroy(Module $module)
    {
        $this->authorizeAccess($module);

        $module->delete();

        return redirect()->route('admin.modules.index')
            ->with('success', 'Módulo eliminado correctamente.');
    }

    /**
     * Reordenar módulos
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'uuid|exists:modules,id',
        ]);

        foreach ($request->order as $index => $id) {
            Module::where('id', $id)
                ->where('company_id', $this->getCompanyId())
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Cambiar estado
     */
    public function toggleStatus(Module $module)
    {
        $this->authorizeAccess($module);

        $newStatus = $module->status === 'active' ? 'inactive' : 'active';
        $module->update(['status' => $newStatus]);

        notify()->success('Estado del módulo actualizado.', 'Éxito');
        return back();
    }

    /**
     * Mostrar módulos por defecto disponibles
     */
    public function showDefaults()
    {
        $categories = DefaultModule::active()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $systems = DefaultModule::active()
            ->select('system_name', 'category')
            ->distinct()
            ->orderBy('system_name')
            ->get()
            ->groupBy('category');

        $defaultModules = DefaultModule::active()
            ->ordered()
            ->get()
            ->groupBy('system_name');

        return view('admin.modules.defaults', compact('categories', 'systems', 'defaultModules'));
    }

    /**
     * Aplicar módulos por defecto de un sistema específico
     */
    public function applyDefaults(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string|exists:default_modules,system_name',
            'module_ids' => 'nullable|array',
            'module_ids.*' => 'uuid|exists:default_modules,id',
        ]);

        $moduleIds = $request->module_ids ?? [];

        if (empty($moduleIds)) {
            return back()->with('warning', 'No seleccionaste ningún módulo.');
        }

        $defaultModules = DefaultModule::whereIn('id', $moduleIds)
            ->where('is_active', true)
            ->get();

        $lastOrder = Module::where('company_id', $this->getCompanyId())
            ->max('sort_order') ?? 0;

        $createdCount = 0;

        foreach ($defaultModules as $index => $defaultModule) {
            Module::create([
                'company_id' => $this->getCompanyId(),
                'label' => $defaultModule->label,
                'description' => $defaultModule->description,
                'type' => $defaultModule->type,
                'url' => $defaultModule->url,
                'target' => $defaultModule->target,
                'modal_id' => $defaultModule->modal_id,
                'icon' => $defaultModule->icon,
                'icon_type' => $defaultModule->icon_type,
                'background_color' => $defaultModule->background_color,
                'is_featured' => $defaultModule->is_featured,
                'group_name' => $defaultModule->group_name,
                'sort_order' => $lastOrder + $index + 1,
                'status' => 'active',
            ]);

            $createdCount++;
        }

        return redirect()->route('admin.modules.index')
            ->with('success', "Se crearon {$createdCount} módulos correctamente.");
    }

    /**
     * Verificar acceso
     */
    private function authorizeAccess(Module $module): void
    {
        if ($module->company_id !== $this->getCompanyId()) {
            abort(403, 'No tienes acceso a este módulo.');
        }
    }
}
