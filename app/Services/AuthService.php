<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;
use App\Models\PasswordResetToken;
use App\Events\UserLoggedIn;
use App\Events\UserRegistered;
use App\Events\PasswordResetRequested;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;

class AuthService
{
    /**
     * TTL para el access token (en minutos)
     */
    protected int $accessTokenTtl;

    /**
     * TTL para el refresh token (en minutos)
     */
    protected int $refreshTokenTtl;

    /**
     * Máximo de sesiones activas por usuario
     */
    protected int $maxActiveSessions;

    public function __construct()
    {
        $this->accessTokenTtl = config('jwt.ttl', 60); // 1 hora
        $this->refreshTokenTtl = config('jwt.refresh_ttl', 20160); // 14 días
        $this->maxActiveSessions = config('auth.max_sessions', 5);
    }

    /**
     * Intentar login con credenciales
     */
    public function attemptLogin(string $email, string $password, string $companyId): ?array
    {
        $user = User::where('email', $email)
                    ->where('company_id', $companyId)
                    ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        if ($user->status !== 'active') {
            return null;
        }

        // Limpiar sesiones excedentes
        $this->cleanupExcessSessions($user);

        // Crear sesión
        $session = $this->createSession($user);

        // Actualizar último login
        $user->updateLastLogin();

        // Disparar evento
        event(new UserLoggedIn($user));

        return [
            'user' => $user,
            'access_token' => $this->generateAccessToken($user, $session),
            'refresh_token' => $this->generateRefreshToken($session),
            'expires_at' => Carbon::now()->addMinutes($this->accessTokenTtl),
        ];
    }

    /**
     * Cerrar sesión
     */
    public function logout(string $sessionToken): bool
    {
        $session = UserSession::where('token', $sessionToken)->first();

        if ($session) {
            $session->delete();
            return true;
        }

        return false;
    }

    /**
     * Cerrar todas las sesiones de un usuario
     */
    public function logoutAllSessions(User $user): int
    {
        return $user->sessions()->delete();
    }

    /**
     * Refrescar token de acceso
     */
    public function refreshToken(string $refreshToken): ?array
    {
        try {
            // Decodificar el refresh token
            JWTAuth::setToken($refreshToken);
            $payload = JWTAuth::getPayload();

            $sessionId = $payload->get('session_id');
            $type = $payload->get('type');

            // Verificar que es un refresh token
            if ($type !== 'refresh') {
                return null;
            }

            $session = UserSession::with('user')
                                   ->where('id', $sessionId)
                                   ->active()->first();

            if (!$session || !$session->user) {
                return null;
            }

            // Generar nuevo access token
            return [
                'access_token' => $this->generateAccessToken($session->user, $session),
                'expires_at' => Carbon::now()->addMinutes($this->accessTokenTtl),
            ];
        } catch (JWTException $e) {
            return null;
        }
    }

    /**
     * Crear sesión para usuario
     */
    protected function createSession(User $user): UserSession
    {
        return UserSession::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'expires_at' => Carbon::now()->addMinutes($this->refreshTokenTtl),
            'created_at' => now(),
        ]);
    }

    /**
     * Generar access token JWT
     */
    protected function generateAccessToken(User $user, UserSession $session): string
    {
        $customClaims = [
            'session_id' => $session->id,
            'company_id' => $user->company_id,
            'role' => $user->role,
            'type' => 'access',
        ];

        return JWTAuth::claims($customClaims)->fromUser($user);
    }

    /**
     * Generar refresh token JWT
     */
    protected function generateRefreshToken(UserSession $session): string
    {
        $customClaims = [
            'session_id' => $session->id,
            'type' => 'refresh',
            'exp' => Carbon::now()->addMinutes($this->refreshTokenTtl)->timestamp,
        ];

        // Crear un token con claims personalizados para refresh
        return JWTAuth::claims($customClaims)->fromUser($session->user);
    }

    /**
     * Validar access token
     */
    public function validateToken(string $token): ?array
    {
        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();

            $sessionId = $payload->get('session_id');
            $type = $payload->get('type');

            // Verificar que es un access token
            if ($type !== 'access') {
                return null;
            }

            // Verificar que la sesión sigue activa
            $session = UserSession::active()
                                   ->where('id', $sessionId)
                                   ->first();

            if (!$session) {
                return null;
            }

            return [
                'user_id' => $payload->get('sub'),
                'company_id' => $payload->get('company_id'),
                'role' => $payload->get('role'),
                'session_id' => $sessionId,
            ];
        } catch (JWTException $e) {
            return null;
        }
    }

    /**
     * Limpiar sesiones excedentes
     */
    protected function cleanupExcessSessions(User $user): void
    {
        $activeSessions = $user->sessions()
                               ->active()
                               ->orderByDesc('created_at')
                               ->get();

        if ($activeSessions->count() >= $this->maxActiveSessions) {
            // Eliminar la sesión más antigua
            $activeSessions->last()->delete();
        }
    }

    /**
     * Solicitar restablecimiento de contraseña
     */
    public function requestPasswordReset(string $email): ?PasswordResetToken
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return null;
        }

        $token = PasswordResetToken::createForEmail($email);

        event(new PasswordResetRequested($user, $token));

        return $token;
    }

    /**
     * Restablecer contraseña
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        $resetToken = PasswordResetToken::findValidToken($token);

        if (!$resetToken) {
            return false;
        }

        $user = User::where('email', $resetToken->email)->first();

        if (!$user) {
            return false;
        }

        // Actualizar contraseña
        $user->update(['password' => Hash::make($newPassword)]);

        // Marcar token como usado
        $resetToken->markAsUsed();

        // Invalidar todas las sesiones existentes
        $this->logoutAllSessions($user);

        return true;
    }

    /**
     * Registrar nuevo usuario
     */
    public function registerUser(array $data): User
    {
        $user = User::create([
            'company_id' => $data['company_id'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'name' => $data['name'],
            'last_name' => $data['last_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] ?? 'user',
            'status' => 'pending',
            'email_verification_token' => Str::random(64),
        ]);

        event(new UserRegistered($user));

        return $user;
    }

    /**
     * Verificar email
     */
    public function verifyEmail(string $token): ?User
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return null;
        }

        $user->markEmailAsVerified();

        return $user;
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->update(['password' => Hash::make($newPassword)]);

        // Invalidar todas las sesiones excepto la actual
        $this->logoutAllSessions($user);

        return true;
    }
}
