<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Interfaces\ImageInterface;

class ImageService
{
    /**
     * Disco de almacenamiento a utilizar
     */
    protected string $disk;

    /**
     * Tipos MIME permitidos
     */
    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ];

    /**
     * Tamaño máximo por defecto en bytes (5MB)
     */
    protected int $maxFileSize = 5242880;

    public function __construct()
    {
        $this->disk = config('filesystems.default', 'local');
    }

    /**
     * Subir una imagen
     */
    public function upload(UploadedFile $file, string $path, array $options = []): array
    {
        // Validar archivo
        $this->validateFile($file, $options);

        // Generar nombre único
        $filename = $this->generateFilename($file);
        $fullPath = trim($path, '/') . '/' . $filename;

        // Optimizar imagen si no es SVG
        if ($file->getMimeType() !== 'image/svg+xml') {
            $content = $this->optimizeImage($file, $options);
            Storage::disk($this->disk)->put($fullPath, $content);
        } else {
            Storage::disk($this->disk)->putFileAs(
                $path,
                $file,
                $filename
            );
        }

        // Obtener metadatos
        return $this->getUploadedFileMetadata($file, $fullPath);
    }

    /**
     * Subir imagen de banner con validaciones específicas
     */
    public function uploadBanner(UploadedFile $file, string $companyId): array
    {
        return $this->upload($file, "companies/{$companyId}/banner", [
            'max_size' => 5242880, // 5MB
            'min_width' => 800,
            'min_height' => 400,
            'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'],
        ]);
    }

    /**
     * Subir logo con validaciones específicas
     */
    public function uploadLogo(UploadedFile $file, string $companyId): array
    {
        return $this->upload($file, "companies/{$companyId}/logo", [
            'max_size' => 1048576, // 1MB
            'resize_width' => 400,
            'resize_height' => 400,
            'allowed_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'],
        ]);
    }

    /**
     * Subir avatar
     */
    public function uploadAvatar(UploadedFile $file, string $userId): array
    {
        return $this->upload($file, "users/{$userId}/avatar", [
            'max_size' => 1048576, // 1MB
            'resize_width' => 200,
            'resize_height' => 200,
            'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'],
        ]);
    }

    /**
     * Redimensionar imagen
     */
    public function resize(string $path, int $width, int $height, bool $maintainAspect = true): string
    {
        $content = Storage::disk($this->disk)->get($path);
        $image = Image::read($content);

        if ($maintainAspect) {
            $image->scale(width: $width, height: $height);
        } else {
            $image->resize($width, $height);
        }

        Storage::disk($this->disk)->put($path, $image->toJpeg(90));

        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Optimizar imagen
     */
    protected function optimizeImage(UploadedFile $file, array $options = []): string
    {
        $image = Image::read($file->getPathname());

        // Redimensionar si se especifica
        if (isset($options['resize_width']) || isset($options['resize_height'])) {
            $width = $options['resize_width'] ?? null;
            $height = $options['resize_height'] ?? null;

            if ($width && $height) {
                $image->cover($width, $height);
            } elseif ($width) {
                $image->scale(width: $width);
            } elseif ($height) {
                $image->scale(height: $height);
            }
        }

        // Rotar según EXIF si es necesario
        $image->orient();

        // Comprimir según tipo
        $quality = $options['quality'] ?? 85;

        return match ($file->getMimeType()) {
            'image/png' => $image->toPng()->toString(),
            'image/gif' => $image->toGif()->toString(),
            'image/webp' => $image->toWebp($quality)->toString(),
            default => $image->toJpeg($quality)->toString(),
        };
    }

    /**
     * Obtener metadatos de una imagen
     */
    public function getMetadata(string $path): array
    {
        if (!Storage::disk($this->disk)->exists($path)) {
            throw new \Exception("El archivo no existe: {$path}");
        }

        $content = Storage::disk($this->disk)->get($path);
        $image = Image::read($content);

        return [
            'path' => $path,
            'url' => Storage::disk($this->disk)->url($path),
            'width' => $image->width(),
            'height' => $image->height(),
            'mime_type' => Storage::disk($this->disk)->mimeType($path),
            'file_size' => Storage::disk($this->disk)->size($path),
        ];
    }

    /**
     * Eliminar imagen
     */
    public function delete(string $path): bool
    {
        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }

        return true;
    }

    /**
     * Validar archivo
     */
    protected function validateFile(UploadedFile $file, array $options = []): void
    {
        $allowedTypes = $options['allowed_types'] ?? $this->allowedMimeTypes;
        $maxSize = $options['max_size'] ?? $this->maxFileSize;

        // Validar tipo MIME
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new \InvalidArgumentException(
                'Tipo de archivo no permitido. Tipos aceptados: ' . implode(', ', $allowedTypes)
            );
        }

        // Validar tamaño
        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException(
                'El archivo excede el tamaño máximo permitido de ' . ($maxSize / 1048576) . 'MB'
            );
        }

        // Validar dimensiones mínimas si se especifican
        if (isset($options['min_width']) || isset($options['min_height'])) {
            if ($file->getMimeType() !== 'image/svg+xml') {
                $image = Image::read($file->getPathname());

                if (isset($options['min_width']) && $image->width() < $options['min_width']) {
                    throw new \InvalidArgumentException(
                        "El ancho de la imagen debe ser al menos {$options['min_width']}px"
                    );
                }

                if (isset($options['min_height']) && $image->height() < $options['min_height']) {
                    throw new \InvalidArgumentException(
                        "La altura de la imagen debe ser al menos {$options['min_height']}px"
                    );
                }
            }
        }
    }

    /**
     * Generar nombre único para archivo
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        return Str::uuid() . '.' . $extension;
    }

    /**
     * Obtener metadatos del archivo subido
     */
    protected function getUploadedFileMetadata(UploadedFile $file, string $path): array
    {
        $width = null;
        $height = null;

        if ($file->getMimeType() !== 'image/svg+xml') {
            try {
                $image = Image::read($file->getPathname());
                $width = $image->width();
                $height = $image->height();
            } catch (\Exception $e) {
                // Ignorar errores de lectura de dimensiones
            }
        }

        return [
            'path' => $path,
            'url' => Storage::disk($this->disk)->url($path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'hash' => md5_file($file->getPathname()),
        ];
    }

    /**
     * Verificar si existe un archivo con el mismo hash
     */
    public function checkDuplicate(UploadedFile $file, string $path): ?string
    {
        $hash = md5_file($file->getPathname());

        // Buscar archivos con el mismo hash en el path
        $files = Storage::disk($this->disk)->files($path);

        foreach ($files as $existingFile) {
            $content = Storage::disk($this->disk)->get($existingFile);
            if (md5($content) === $hash) {
                return Storage::disk($this->disk)->url($existingFile);
            }
        }

        return null;
    }
}
