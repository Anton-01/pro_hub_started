<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBulkEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Lista de destinatarios.
     */
    protected array $recipients;

    /**
     * Clase del Mailable a enviar.
     */
    protected string $mailableClass;

    /**
     * Datos para el Mailable.
     */
    protected array $mailableData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $recipients, string $mailableClass, array $mailableData = [])
    {
        $this->recipients = $recipients;
        $this->mailableClass = $mailableClass;
        $this->mailableData = $mailableData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->recipients as $recipient) {
            try {
                $mailable = new $this->mailableClass(...array_values($this->mailableData));
                Mail::to($recipient)->send($mailable);

                Log::info("Email enviado exitosamente a: {$recipient}");
            } catch (\Exception $e) {
                Log::error("Error enviando email a: {$recipient}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job de envío de emails masivos falló", [
            'error' => $exception->getMessage(),
            'recipients_count' => count($this->recipients),
        ]);
    }
}
