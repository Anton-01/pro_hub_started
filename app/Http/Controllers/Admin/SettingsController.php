<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyConfiguration;
use App\Models\CacheSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class SettingsController extends Controller
{
    private function getCompany(): Company
    {
        return auth()->user()->company;
    }

    /**
     * Mostrar página de configuración
     */
    public function index()
    {
        $company = $this->getCompany();
        $config = $company->configuration ?? new CompanyConfiguration();
        $cacheSettings = $company->cacheSettings ?? new CacheSetting();

        return view('admin.settings.index', compact('company', 'config', 'cacheSettings'));
    }

    /**
     * Actualizar configuración general de la empresa
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
        ]);

        $this->getCompany()->update($validated);

        return back()->with('success', 'Información general actualizada.');
    }

    /**
     * Actualizar configuración de branding
     */
    public function updateBranding(Request $request)
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'header_text' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:255',
            'show_calendar' => 'boolean',
            'show_news_ticker' => 'boolean',
            'show_contacts' => 'boolean',
        ]);

        $config = $this->getOrCreateConfig();

        $config->update([
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'header_text' => $validated['header_text'] ?? null,
            'footer_text' => $validated['footer_text'] ?? null,
            'show_calendar' => $request->boolean('show_calendar'),
            'show_news_ticker' => $request->boolean('show_news_ticker'),
            'show_contacts' => $request->boolean('show_contacts'),
        ]);

        $this->clearCompanyCache();

        return back()->with('success', 'Configuración de branding actualizada.');
    }

    /**
     * Actualizar tema de colores
     */
    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'accent_color' => 'nullable|string|max:20',
            'background_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'error_color' => 'nullable|string|max:20',
            'success_color' => 'nullable|string|max:20',
            'warning_color' => 'nullable|string|max:20',
            'module_bg_color' => 'nullable|string|max:20',
            'module_hover_color' => 'nullable|string|max:20',
        ]);

        $config = $this->getOrCreateConfig();
        $config->update($validated);

        $this->clearCompanyCache();

        return back()->with('success', 'Tema de colores actualizado.');
    }

    /**
     * Subir logo
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|max:2048',
        ]);

        $config = $this->getOrCreateConfig();

        // Eliminar logo anterior
        if ($config->logo_url) {
            Storage::disk('public')->delete($config->logo_url);
        }

        $file = $request->file('logo');
        $path = $file->store('logos', 'public');

        // Obtener dimensiones
        $image = Image::read(Storage::disk('public')->path($path));

        $config->update([
            'logo_url' => $path,
            'logo_width' => $image->width(),
            'logo_height' => $image->height(),
            'logo_file_size' => $file->getSize(),
            'logo_mime_type' => $file->getMimeType(),
        ]);

        $this->clearCompanyCache();

        return back()->with('success', 'Logo actualizado correctamente.');
    }

    /**
     * Eliminar logo
     */
    public function deleteLogo()
    {
        $config = $this->getOrCreateConfig();

        if ($config->logo_url) {
            Storage::disk('public')->delete($config->logo_url);

            $config->update([
                'logo_url' => null,
                'logo_width' => null,
                'logo_height' => null,
                'logo_file_size' => null,
                'logo_mime_type' => null,
            ]);
        }

        $this->clearCompanyCache();

        return back()->with('success', 'Logo eliminado.');
    }

    /**
     * Subir favicon
     */
    public function uploadFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|image|max:512|dimensions:max_width=256,max_height=256',
        ]);

        $config = $this->getOrCreateConfig();

        if ($config->favicon_url) {
            Storage::disk('public')->delete($config->favicon_url);
        }

        $path = $request->file('favicon')->store('favicons', 'public');
        $config->update(['favicon_url' => $path]);

        $this->clearCompanyCache();

        return back()->with('success', 'Favicon actualizado.');
    }

    /**
     * Actualizar configuración de cache
     */
    public function updateCache(Request $request)
    {
        $validated = $request->validate([
            'modules_ttl' => 'required|integer|min:60|max:86400',
            'contacts_ttl' => 'required|integer|min:60|max:86400',
            'events_ttl' => 'required|integer|min:60|max:86400',
            'news_ttl' => 'required|integer|min:10|max:3600',
            'banner_ttl' => 'required|integer|min:60|max:86400',
            'config_ttl' => 'required|integer|min:60|max:86400',
        ]);

        $cacheSettings = CacheSetting::firstOrCreate(
            ['company_id' => $this->getCompany()->id],
            $validated
        );

        $cacheSettings->update($validated);

        return back()->with('success', 'Configuración de cache actualizada.');
    }

    /**
     * Limpiar cache de la empresa
     */
    public function clearCache()
    {
        $this->clearCompanyCache();

        return back()->with('success', 'Cache limpiado correctamente.');
    }

    /**
     * Obtener o crear configuración
     */
    private function getOrCreateConfig(): CompanyConfiguration
    {
        return CompanyConfiguration::firstOrCreate(
            ['company_id' => $this->getCompany()->id],
            [
                'primary_color' => '#3b82f6',
                'secondary_color' => '#64748b',
                'show_calendar' => true,
                'show_news_ticker' => true,
                'show_contacts' => true,
            ]
        );
    }

    /**
     * Limpiar cache de la empresa
     */
    private function clearCompanyCache(): void
    {
        $companyId = $this->getCompany()->id;

        Cache::forget("company.{$companyId}.config");
        Cache::forget("company.{$companyId}.modules");
        Cache::forget("company.{$companyId}.contacts");
        Cache::forget("company.{$companyId}.events");
        Cache::forget("company.{$companyId}.news");
        Cache::forget("company.{$companyId}.banners");
    }
}
