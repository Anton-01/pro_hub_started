<?php

namespace App\Services;

use App\Models\User;
use App\Models\PasswordResetToken;
use App\Mail\WelcomeAdminEmail;
use App\Mail\WelcomeUserEmail;
use App\Mail\PasswordResetEmail;
use App\Mail\PasswordChangedEmail;
use App\Mail\NewUserNotification;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Enviar email de bienvenida a administrador
     */
    public function sendWelcomeAdmin(User $user, string $temporaryPassword = null): void
    {
        Mail::to($user->email)
            ->queue(new WelcomeAdminEmail($user, $temporaryPassword));
    }

    /**
     * Enviar email de bienvenida a usuario
     */
    public function sendWelcomeUser(User $user): void
    {
        Mail::to($user->email)
            ->queue(new WelcomeUserEmail($user));
    }

    /**
     * Enviar email de restablecimiento de contraseña
     */
    public function sendPasswordReset(User $user, PasswordResetToken $token): void
    {
        Mail::to($user->email)
            ->queue(new PasswordResetEmail($user, $token));
    }

    /**
     * Enviar notificación de cambio de contraseña
     */
    public function sendPasswordChanged(User $user): void
    {
        Mail::to($user->email)
            ->queue(new PasswordChangedEmail($user));
    }

    /**
     * Notificar a admins sobre nuevo usuario
     */
    public function notifyNewUser(User $newUser): void
    {
        $admins = User::where('company_id', $newUser->company_id)
            ->whereIn('role', ['super_admin', 'admin'])
            ->where('id', '!=', $newUser->id)
            ->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)
                ->queue(new NewUserNotification($admin, $newUser));
        }
    }

    /**
     * Enviar email de verificación
     */
    public function sendEmailVerification(User $user): void
    {
        $verificationUrl = $this->buildVerificationUrl($user);

        Mail::to($user->email)->queue(new \App\Mail\EmailVerification($user, $verificationUrl));
    }

    /**
     * Construir URL de verificación
     */
    protected function buildVerificationUrl(User $user): string
    {
        $frontendUrl = config('app.frontend_url', config('app.url'));

        return "{$frontendUrl}/verify-email?token={$user->email_verification_token}";
    }

    /**
     * Construir URL de restablecimiento
     */
    public function buildPasswordResetUrl(PasswordResetToken $token): string
    {
        $frontendUrl = config('app.frontend_url', config('app.url'));

        return "{$frontendUrl}/reset-password?token={$token->token}";
    }
}
