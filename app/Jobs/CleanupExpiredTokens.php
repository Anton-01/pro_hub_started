<?php

namespace App\Jobs;

use App\Models\UserSession;
use App\Models\PasswordResetToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupExpiredTokens implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Limpiar sesiones expiradas
        $expiredSessions = UserSession::expired()->delete();
        Log::info("Sesiones expiradas eliminadas: {$expiredSessions}");

        // Limpiar tokens de reset expirados
        $expiredTokens = PasswordResetToken::expired()->delete();
        Log::info("Tokens de reset expirados eliminados: {$expiredTokens}");
    }
}
