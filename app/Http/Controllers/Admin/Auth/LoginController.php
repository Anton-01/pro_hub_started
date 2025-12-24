<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetToken;
use App\Mail\PasswordResetEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Rate limiting
        $key = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Demasiados intentos. Intenta de nuevo en {$seconds} segundos.",
            ]);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Buscar usuario con rol admin o super_admin
        $user = User::where('email', $credentials['email'])
            ->whereIn('role', ['admin', 'super_admin'])
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        // Verificar si el usuario está activo
        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => 'Tu cuenta ha sido desactivada. Contacta al administrador.',
            ]);
        }

        // Login exitoso
        RateLimiter::clear($key);
        Auth::login($user, $remember);
        $user->updateLastLogin();

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', '¡Bienvenido de vuelta, ' . $user->name . '!');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Has cerrado sesión correctamente.');
    }

    /**
     * Mostrar formulario de recuperación de contraseña
     */
    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Enviar enlace de recuperación
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
            ->whereIn('role', ['admin', 'super_admin'])
            ->first();

        // Siempre mostrar el mismo mensaje por seguridad
        $message = 'Si el correo existe en nuestro sistema, recibirás un enlace de recuperación.';

        if (!$user) {
            return back()->with('success', $message);
        }

        // Invalidar tokens anteriores
        PasswordResetToken::where('user_id', $user->id)->delete();

        // Crear nuevo token
        $token = Str::random(64);
        PasswordResetToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'expires_at' => now()->addHour(),
        ]);

        // Enviar email
        Mail::to($user->email)->send(new PasswordResetEmail($user, $token));

        return back()->with('success', $message);
    }

    /**
     * Mostrar formulario de reset de contraseña
     */
    public function showResetForm(string $token)
    {
        return view('admin.auth.reset-password', ['token' => $token]);
    }

    /**
     * Procesar reset de contraseña
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)
            ->whereIn('role', ['admin', 'super_admin'])
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'No encontramos un usuario con ese correo.',
            ]);
        }

        $resetToken = PasswordResetToken::where('user_id', $user->id)
            ->where('token', hash('sha256', $request->token))
            ->where('expires_at', '>', now())
            ->where('used_at', null)
            ->first();

        if (!$resetToken) {
            throw ValidationException::withMessages([
                'token' => 'El enlace de recuperación es inválido o ha expirado.',
            ]);
        }

        // Actualizar contraseña
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Marcar token como usado
        $resetToken->update(['used_at' => now()]);

        return redirect()->route('admin.login')
            ->with('success', 'Tu contraseña ha sido actualizada. Ya puedes iniciar sesión.');
    }
}
