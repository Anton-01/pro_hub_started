<?php

namespace App\Services;

use App\Models\CacheSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Closure;

class CacheService
{
    /**
     * Prefijo para las claves de caché
     */
    protected string $prefix = 'panel_empresarial';

    /**
     * Driver de caché a utilizar
     */
    protected string $driver;

    public function __construct()
    {
        $this->driver = config('cache.default', 'redis');
    }

    /**
     * Obtener o establecer un valor en caché
     */
    public function getOrSet(string $key, Closure $callback, int $ttl, array $tags = []): mixed
    {
        $fullKey = $this->buildKey($key);

        if (!empty($tags) && $this->supportsTagging()) {
            return Cache::tags($tags)->remember($fullKey, $ttl, $callback);
        }

        return Cache::remember($fullKey, $ttl, $callback);
    }

    /**
     * Obtener un valor de caché
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($this->buildKey($key), $default);
    }

    /**
     * Establecer un valor en caché
     */
    public function set(string $key, mixed $value, int $ttl, array $tags = []): bool
    {
        $fullKey = $this->buildKey($key);

        if (!empty($tags) && $this->supportsTagging()) {
            return Cache::tags($tags)->put($fullKey, $value, $ttl);
        }

        return Cache::put($fullKey, $value, $ttl);
    }

    /**
     * Eliminar un valor de caché
     */
    public function forget(string $key): bool
    {
        return Cache::forget($this->buildKey($key));
    }

    /**
     * Invalidar caché por tag
     */
    public function invalidateByTag(string $tag): bool
    {
        if (!$this->supportsTagging()) {
            return false;
        }

        Cache::tags([$tag])->flush();
        return true;
    }

    /**
     * Invalidar caché de una empresa
     */
    public function invalidateCompanyCache(string $companyId, ?string $type = null): bool
    {
        if ($type) {
            $tag = "company:{$companyId}:{$type}";
            $this->invalidateByTag($tag);
        } else {
            // Invalidar todos los tipos
            $types = ['modules', 'contacts', 'events', 'news', 'banner', 'config'];
            foreach ($types as $t) {
                $this->invalidateByTag("company:{$companyId}:{$t}");
            }
        }

        return true;
    }

    /**
     * Calentar caché para una empresa
     */
    public function warmCache(string $companyId): array
    {
        $results = [];
        $settings = CacheSetting::getForCompany($companyId);

        // Precargar módulos
        $results['modules'] = $this->warmModulesCache($companyId, $settings->modules_ttl);

        // Precargar contactos
        $results['contacts'] = $this->warmContactsCache($companyId, $settings->contacts_ttl);

        // Precargar eventos
        $results['events'] = $this->warmEventsCache($companyId, $settings->events_ttl);

        // Precargar noticias
        $results['news'] = $this->warmNewsCache($companyId, $settings->news_ttl);

        // Precargar banner
        $results['banner'] = $this->warmBannerCache($companyId, $settings->banner_ttl);

        // Precargar configuración
        $results['config'] = $this->warmConfigCache($companyId, $settings->config_ttl);

        return $results;
    }

    /**
     * Obtener estadísticas de caché
     */
    public function getStats(): array
    {
        $stats = [
            'driver' => $this->driver,
            'supports_tagging' => $this->supportsTagging(),
            'memory' => null,
            'keys_count' => null,
        ];

        if ($this->driver === 'redis') {
            try {
                $info = Redis::info('memory');
                $stats['memory'] = [
                    'used' => $info['used_memory_human'] ?? null,
                    'peak' => $info['used_memory_peak_human'] ?? null,
                ];

                $keys = Redis::keys("{$this->prefix}:*");
                $stats['keys_count'] = count($keys);
            } catch (\Exception $e) {
                // Redis no disponible
            }
        }

        return $stats;
    }

    /**
     * Limpiar toda la caché
     */
    public function flush(): bool
    {
        return Cache::flush();
    }

    /**
     * Construir clave completa
     */
    protected function buildKey(string $key): string
    {
        return "{$this->prefix}:{$key}";
    }

    /**
     * Verificar si el driver soporta tagging
     */
    protected function supportsTagging(): bool
    {
        return in_array($this->driver, ['redis', 'memcached', 'array']);
    }

    /**
     * Calentar caché de módulos
     */
    protected function warmModulesCache(string $companyId, int $ttl): bool
    {
        $key = "company:{$companyId}:modules";
        $tags = ["company:{$companyId}:modules"];

        return $this->set($key, function () use ($companyId) {
            return \App\Models\Module::forCompany($companyId)
                ->active()
                ->ordered()
                ->get();
        }, $ttl, $tags) !== false;
    }

    /**
     * Calentar caché de contactos
     */
    protected function warmContactsCache(string $companyId, int $ttl): bool
    {
        $key = "company:{$companyId}:contacts";
        $tags = ["company:{$companyId}:contacts"];

        return $this->set($key, function () use ($companyId) {
            return \App\Models\Contact::forCompany($companyId)
                ->active()
                ->ordered()
                ->get();
        }, $ttl, $tags) !== false;
    }

    /**
     * Calentar caché de eventos
     */
    protected function warmEventsCache(string $companyId, int $ttl): bool
    {
        $key = "company:{$companyId}:events";
        $tags = ["company:{$companyId}:events"];

        return $this->set($key, function () use ($companyId) {
            return \App\Models\CalendarEvent::forCompany($companyId)
                ->active()
                ->currentMonth()
                ->get();
        }, $ttl, $tags) !== false;
    }

    /**
     * Calentar caché de noticias
     */
    protected function warmNewsCache(string $companyId, int $ttl): bool
    {
        $key = "company:{$companyId}:news";
        $tags = ["company:{$companyId}:news"];

        return $this->set($key, function () use ($companyId) {
            return \App\Models\News::forCompany($companyId)
                ->currentlyVisible()
                ->ordered()
                ->get();
        }, $ttl, $tags) !== false;
    }

    /**
     * Calentar caché de banner
     */
    protected function warmBannerCache(string $companyId, int $ttl): bool
    {
        $key = "company:{$companyId}:banner";
        $tags = ["company:{$companyId}:banner"];

        return $this->set($key, function () use ($companyId) {
            return \App\Models\BannerImage::forCompany($companyId)
                ->active()
                ->ordered()
                ->get();
        }, $ttl, $tags) !== false;
    }

    /**
     * Calentar caché de configuración
     */
    protected function warmConfigCache(string $companyId, int $ttl): bool
    {
        $key = "company:{$companyId}:config";
        $tags = ["company:{$companyId}:config"];

        return $this->set($key, function () use ($companyId) {
            return \App\Models\Company::with('configuration')
                ->find($companyId);
        }, $ttl, $tags) !== false;
    }

    /**
     * Obtener módulos de caché
     */
    public function getModules(string $companyId)
    {
        $settings = CacheSetting::getForCompany($companyId);
        $key = "company:{$companyId}:modules";
        $tags = ["company:{$companyId}:modules"];

        return $this->getOrSet($key, function () use ($companyId) {
            return \App\Models\Module::forCompany($companyId)
                ->active()
                ->ordered()
                ->get();
        }, $settings->modules_ttl, $tags);
    }

    /**
     * Obtener contactos de caché
     */
    public function getContacts(string $companyId, ?string $search = null)
    {
        $settings = CacheSetting::getForCompany($companyId);

        // Si hay búsqueda, no usar caché
        if ($search) {
            return \App\Models\Contact::forCompany($companyId)
                ->active()
                ->search($search)
                ->ordered()
                ->get();
        }

        $key = "company:{$companyId}:contacts";
        $tags = ["company:{$companyId}:contacts"];

        return $this->getOrSet($key, function () use ($companyId) {
            return \App\Models\Contact::forCompany($companyId)
                ->active()
                ->ordered()
                ->get();
        }, $settings->contacts_ttl, $tags);
    }

    /**
     * Obtener eventos de caché
     */
    public function getEvents(string $companyId, ?int $month = null, ?int $year = null)
    {
        $settings = CacheSetting::getForCompany($companyId);
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $key = "company:{$companyId}:events:{$year}:{$month}";
        $tags = ["company:{$companyId}:events"];

        return $this->getOrSet($key, function () use ($companyId, $month, $year) {
            return \App\Models\CalendarEvent::forCompany($companyId)
                ->active()
                ->inMonth($month, $year)
                ->get();
        }, $settings->events_ttl, $tags);
    }

    /**
     * Obtener noticias de caché
     */
    public function getNews(string $companyId)
    {
        $settings = CacheSetting::getForCompany($companyId);
        $key = "company:{$companyId}:news";
        $tags = ["company:{$companyId}:news"];

        return $this->getOrSet($key, function () use ($companyId) {
            return \App\Models\News::forCompany($companyId)
                ->currentlyVisible()
                ->ordered()
                ->get();
        }, $settings->news_ttl, $tags);
    }

    /**
     * Obtener banner de caché
     */
    public function getBanner(string $companyId)
    {
        $settings = CacheSetting::getForCompany($companyId);
        $key = "company:{$companyId}:banner";
        $tags = ["company:{$companyId}:banner"];

        return $this->getOrSet($key, function () use ($companyId) {
            return \App\Models\BannerImage::forCompany($companyId)
                ->active()
                ->ordered()
                ->get();
        }, $settings->banner_ttl, $tags);
    }

    /**
     * Obtener configuración de caché
     */
    public function getConfiguration(string $companyId)
    {
        $settings = CacheSetting::getForCompany($companyId);
        $key = "company:{$companyId}:config";
        $tags = ["company:{$companyId}:config"];

        return $this->getOrSet($key, function () use ($companyId) {
            return \App\Models\CompanyConfiguration::where('company_id', $companyId)->first();
        }, $settings->config_ttl, $tags);
    }
}
