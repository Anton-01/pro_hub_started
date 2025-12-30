<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannerImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class BannerImageController extends Controller
{
    private function getCompanyId(): string
    {
        return auth()->user()->company_id;
    }

    /**
     * Listar imágenes del banner
     */
    public function index(Request $request)
    {
        $banners = BannerImage::where('company_id', $this->getCompanyId())
            ->orderBy('order')
            ->paginate(12);

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Mostrar formulario de subida
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Guardar nueva imagen
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:5120', // 5MB máximo
            'alt_text' => 'nullable|string|max:255',
            'link_url' => 'nullable|url|max:500',
            'link_target' => 'nullable|in:_self,_blank',
            'status' => 'required|in:active,inactive',
        ]);

        $file = $request->file('image');
        $path = $file->store('banners', 'public');

        // Obtener dimensiones de la imagen
        $image = Image::read(Storage::disk('public')->path($path));
        $width = $image->width();
        $height = $image->height();

        $lastOrder = BannerImage::where('company_id', $this->getCompanyId())
            ->max('order') ?? 0;

        BannerImage::create([
            'company_id' => $this->getCompanyId(),
            'url' => $path,
            'alt_text' => $validated['alt_text'] ?? null,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'link_url' => $validated['link_url'] ?? null,
            'link_target' => $validated['link_target'] ?? '_self',
            'order' => $lastOrder + 1,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Imagen subida correctamente.');
    }

    /**
     * Mostrar imagen
     */
    public function show(BannerImage $banner)
    {
        $this->authorizeAccess($banner);

        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Eliminar imagen
     */
    public function destroy(BannerImage $banner)
    {
        $this->authorizeAccess($banner);

        Storage::disk('public')->delete($banner->url);
        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Imagen eliminada correctamente.');
    }

    /**
     * Reordenar imágenes
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'uuid|exists:banner_images,id',
        ]);

        foreach ($request->ids as $index => $id) {
            BannerImage::where('id', $id)
                ->where('company_id', $this->getCompanyId())
                ->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Cambiar estado
     */
    public function toggleStatus(BannerImage $banner)
    {
        $this->authorizeAccess($banner);

        $newStatus = $banner->status === 'active' ? 'inactive' : 'active';
        $banner->update(['status' => $newStatus]);

        return back()->with('success', 'Estado de la imagen actualizado.');
    }

    private function authorizeAccess(BannerImage $banner): void
    {
        if ($banner->company_id !== $this->getCompanyId()) {
            abort(403, 'No tienes acceso a esta imagen.');
        }
    }
}
