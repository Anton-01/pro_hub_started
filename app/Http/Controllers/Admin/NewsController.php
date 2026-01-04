<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Mckenziearts\Notify\Exceptions\InvalidNotificationException;

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
        $query = News::where('company_id', $this->getCompanyId())->with('creator');

        if ($request->filled('search')) {
            $query->where('content', 'ilike', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('priority')) {
            if ($request->priority == '1') {
                $query->where('priority', '>', 0);
            } else {
                $query->where('priority', 0);
            }
        }

        $news = $query->orderBy('priority', 'desc')
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
     * @throws InvalidNotificationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
            'url' => 'nullable|url|max:500',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'priority' => 'boolean',
            'status' => 'required|in:active,inactive',
        ]);

        News::create([
            'company_id' => $this->getCompanyId(),
            'created_by' => auth()->id(),
            'title' => $validated['content'],
            'content' => $validated['content'],
            'url' => $validated['url'] ?? null,
            'published_at' => $validated['published_at'] ?? now(),
            'expires_at' => $validated['expires_at'] ?? null,
            'priority' => $request->boolean('priority') ? 1 : 0,
            'status' => $validated['status'],
        ]);

        notify()->success()->message('Noticia creada correctamente.')->send();
        return redirect()->route('admin.news.index');
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
            'content' => 'required|string|max:500',
            'url' => 'nullable|url|max:500',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'priority' => 'nullable|integer|min:0|max:10',
            'status' => 'required|in:active,inactive',
        ]);

        $news->update([
            'title' => $validated['content'],
            'content' => $validated['content'],
            'url' => $validated['url'] ?? null,
            'published_at' => $validated['published_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'priority' => $request->boolean('priority') ? 1 : 0,
            'status' => $validated['status'],
        ]);

        notify()->success()->message('Noticia actualizada correctamente.')->send();
        return redirect()->route('admin.news.index');
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
     * @throws InvalidNotificationException
     */
    public function toggleStatus(News $news)
    {
        $this->authorizeAccess($news);

        $newStatus = $news->status === 'active' ? 'inactive' : 'active';
        $news->update(['status' => $newStatus]);

        notify()->success('Estado de la noticia actualizado.', 'Éxito')->send();
        return back();
    }

    private function authorizeAccess(News $news): void
    {
        if ($news->company_id !== $this->getCompanyId()) {
            abort(403, 'No tienes acceso a esta noticia.');
        }
    }
}
