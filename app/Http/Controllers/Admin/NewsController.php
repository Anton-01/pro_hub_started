<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    private function getCompanyId(): string
    {
        return auth()->user()->company_id;
    }

    /**
     * Listar noticias
     */
    public function index(Request $request)
    {
        $query = News::where('company_id', $this->getCompanyId());

        if ($request->filled('search')) {
            $query->where('text', 'ilike', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('priority_only')) {
            $query->where('is_priority', true);
        }

        $news = $query->orderBy('is_priority', 'desc')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.news.create');
    }

    /**
     * Guardar nueva noticia
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:500',
            'url' => 'nullable|url|max:500',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_priority' => 'boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $lastOrder = News::where('company_id', $this->getCompanyId())
            ->max('sort_order') ?? 0;

        News::create([
            'company_id' => $this->getCompanyId(),
            'created_by' => auth()->id(),
            'text' => $validated['text'],
            'url' => $validated['url'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_priority' => $request->boolean('is_priority'),
            'sort_order' => $lastOrder + 1,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.news.index')
            ->with('success', 'Noticia creada correctamente.');
    }

    /**
     * Mostrar noticia
     */
    public function show(News $news)
    {
        $this->authorizeAccess($news);

        return view('admin.news.show', compact('news'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(News $news)
    {
        $this->authorizeAccess($news);

        return view('admin.news.edit', compact('news'));
    }

    /**
     * Actualizar noticia
     */
    public function update(Request $request, News $news)
    {
        $this->authorizeAccess($news);

        $validated = $request->validate([
            'text' => 'required|string|max:500',
            'url' => 'nullable|url|max:500',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_priority' => 'boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $news->update([
            'text' => $validated['text'],
            'url' => $validated['url'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_priority' => $request->boolean('is_priority'),
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.news.index')
            ->with('success', 'Noticia actualizada correctamente.');
    }

    /**
     * Eliminar noticia
     */
    public function destroy(News $news)
    {
        $this->authorizeAccess($news);

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'Noticia eliminada correctamente.');
    }

    /**
     * Cambiar estado
     */
    public function toggleStatus(News $news)
    {
        $this->authorizeAccess($news);

        $newStatus = $news->status === 'active' ? 'inactive' : 'active';
        $news->update(['status' => $newStatus]);

        return back()->with('success', 'Estado de la noticia actualizado.');
    }

    private function authorizeAccess(News $news): void
    {
        if ($news->company_id !== $this->getCompanyId()) {
            abort(403, 'No tienes acceso a esta noticia.');
        }
    }
}
