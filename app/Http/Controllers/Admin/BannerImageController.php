<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannerImageRequest;
use App\Models\BannerImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mckenziearts\Notify\Exceptions\InvalidNotificationException;

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
     * @throws InvalidNotificationException
     */
    public function store(BannerImageRequest $request)
    {
        $validated = $request->validated();

        $file = $request->file('image');
        $path = $file->store('banners', 'public');

        // Obtener dimensiones de la imagen
        $imagePath = Storage::disk('public')->path($path);
        [$width, $height] = getimagesize($imagePath);

        $lastOrder = BannerImage::where('company_id', $this->getCompanyId())->max('order') ?? 0;

        BannerImage::create([
            'company_id' => $this->getCompanyId(),
            'image_path' => $path,
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

        notify()->success()->message('Imagen subida correctamente')->send();
        return redirect()->route('admin.banners.index');
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
     * Mostrar formulario de edición
     */
    public function edit(BannerImage $banner)
    {
        $this->authorizeAccess($banner);

        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Actualizar imagen
     * @throws InvalidNotificationException
     */
    public function update(Request $request, BannerImage $banner)
    {
        $this->authorizeAccess($banner);

        $validated = $request->validate([
            'image' => 'nullable|image|max:5120', // 5MB máximo
            'alt_text' => 'nullable|string|max:255',
            'link_url' => 'nullable|url|max:500',
            'link_target' => 'nullable|in:_self,_blank',
            'status' => 'required|in:active,inactive',
        ]);

        // Si se sube una nueva imagen
        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior
            Storage::disk('public')->delete($banner->image_path);

            $file = $request->file('image');
            $path = $file->store('banners', 'public');

            // Obtener dimensiones de la imagen
            $imagePath = Storage::disk('public')->path($path);
            [$width, $height] = getimagesize($imagePath);

            $banner->update([
                'image_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'width' => $width,
                'height' => $height,
            ]);
        }

        // Actualizar los demás campos
        $banner->update([
            'alt_text' => $validated['alt_text'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'link_target' => $validated['link_target'] ?? '_self',
            'status' => $validated['status'],
        ]);

        notify()->success()->message('Banner actualizado correctamente')->send();
        return redirect()->route('admin.banners.index');
    }

    /**
     * Eliminar imagen
     * @throws InvalidNotificationException
     */
    public function destroy(BannerImage $banner)
    {
        $this->authorizeAccess($banner);

        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();

        notify()->success()->message('Imagen eliminada correctamente')->send();
        return redirect()->route('admin.banners.index');
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
     * @throws InvalidNotificationException
     */
    public function toggleStatus(Request $request, BannerImage $banner)
    {
        $this->authorizeAccess($banner);

        $newStatus = $banner->status === 'active' ? 'inactive' : 'active';
        $banner->update(['status' => $newStatus]);
        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Estado actualizado correctamente'
            ]);
        }

        notify()->success()->message('Estado de la imagen actualizado')->send();
        return back();
    }

    private function authorizeAccess(BannerImage $banner): void
    {
        if ($banner->company_id !== $this->getCompanyId()) {
            abort(403, 'No tienes acceso a esta imagen.');
        }
    }
}
