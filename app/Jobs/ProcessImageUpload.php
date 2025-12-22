<?php

namespace App\Jobs;

use App\Services\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessImageUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Ruta de la imagen a procesar.
     */
    protected string $imagePath;

    /**
     * Opciones de procesamiento.
     */
    protected array $options;

    /**
     * Create a new job instance.
     */
    public function __construct(string $imagePath, array $options = [])
    {
        $this->imagePath = $imagePath;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(ImageService $imageService): void
    {
        try {
            // Redimensionar si se especifican dimensiones
            if (isset($this->options['width']) || isset($this->options['height'])) {
                $imageService->resize(
                    $this->imagePath,
                    $this->options['width'] ?? null,
                    $this->options['height'] ?? null
                );
            }

            Log::info("Imagen procesada exitosamente: {$this->imagePath}");
        } catch (\Exception $e) {
            Log::error("Error procesando imagen: {$this->imagePath}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job de procesamiento de imagen falló: {$this->imagePath}", [
            'error' => $exception->getMessage(),
        ]);
    }
}
