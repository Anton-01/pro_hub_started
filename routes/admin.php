<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CalendarEventController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\BannerImageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\StatisticsController;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
|
| Rutas del panel de administración.
| Prefix: /admin
| Middleware: web (aplicado en bootstrap/app.php)
|
*/

// ===========================================
// Rutas de Autenticación (públicas)
// ===========================================
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');

    Route::get('forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
    Route::get('reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [LoginController::class, 'resetPassword'])->name('password.update');
});

// ===========================================
// Rutas Protegidas (requieren autenticación admin)
// ===========================================
Route::middleware('admin')->group(function () {

    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // =========================================
    // Rutas Solo para Super Admin
    // =========================================
    Route::middleware('super_admin')->group(function () {

        // Empresas (CRUD completo)
        Route::resource('companies', CompanyController::class);
        Route::patch('companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])
            ->name('companies.toggle-status');

        // Estadísticas globales
        Route::prefix('statistics')->name('statistics.')->group(function () {
            Route::get('/', [StatisticsController::class, 'index'])->name('index');
            Route::get('/companies', [StatisticsController::class, 'companies'])->name('companies');
            Route::get('/users', [StatisticsController::class, 'users'])->name('users');
            Route::get('/activity', [StatisticsController::class, 'activity'])->name('activity');
            Route::get('/export', [StatisticsController::class, 'export'])->name('export');
        });

        // Logs de actividad global
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });

    // =========================================
    // Rutas para Admin y Super Admin
    // =========================================

    // Usuarios de la empresa
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle-status');

    // Crear administradores (solo primary admin o super admin)
    Route::middleware('primary_admin')->group(function () {
        Route::get('users/create-admin', [UserController::class, 'createAdmin'])->name('users.create-admin');
        Route::post('users/store-admin', [UserController::class, 'storeAdmin'])->name('users.store-admin');
    });

    // Módulos del portal
    Route::resource('modules', ModuleController::class);
    Route::post('modules/reorder', [ModuleController::class, 'reorder'])->name('modules.reorder');
    Route::patch('modules/{module}/toggle-status', [ModuleController::class, 'toggleStatus'])
        ->name('modules.toggle-status');

    // Módulos por defecto (plantillas)
    Route::get('modules-defaults', [ModuleController::class, 'showDefaults'])->name('modules.defaults');
    Route::post('modules-defaults/apply', [ModuleController::class, 'applyDefaults'])->name('modules.defaults.apply');

    // Contactos / Directorio
    Route::resource('contacts', ContactController::class);
    Route::post('contacts/reorder', [ContactController::class, 'reorder'])->name('contacts.reorder');
    Route::patch('contacts/{contact}/toggle-status', [ContactController::class, 'toggleStatus'])
        ->name('contacts.toggle-status');

    // Importación masiva de contactos
    Route::get('contacts-import', [ContactController::class, 'importView'])->name('contacts.import');
    Route::post('contacts-import', [ContactController::class, 'import'])->name('contacts.import.process');
    Route::get('contacts-template', [ContactController::class, 'downloadTemplate'])->name('contacts.template');

    // Eventos de calendario
    Route::resource('events', CalendarEventController::class);
    Route::patch('events/{event}/toggle-status', [CalendarEventController::class, 'toggleStatus'])
        ->name('events.toggle-status');

    // Noticias / News Ticker
    Route::resource('news', NewsController::class);
    Route::patch('news/{news}/toggle-status', [NewsController::class, 'toggleStatus'])
        ->name('news.toggle-status');

    // Imágenes del banner/carrusel
    Route::resource('banners', BannerImageController::class);
    Route::post('banners/reorder', [BannerImageController::class, 'reorder'])->name('banners.reorder');
    Route::patch('banners/{banner}/toggle-status', [BannerImageController::class, 'toggleStatus'])
        ->name('banners.toggle-status');

    // Configuración de la empresa
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/general', [SettingsController::class, 'updateGeneral'])->name('general');
        Route::put('/branding', [SettingsController::class, 'updateBranding'])->name('branding');
        Route::put('/theme', [SettingsController::class, 'updateTheme'])->name('theme');
        Route::post('/logo', [SettingsController::class, 'uploadLogo'])->name('logo');
        Route::delete('/logo', [SettingsController::class, 'deleteLogo'])->name('logo.delete');
        Route::post('/favicon', [SettingsController::class, 'uploadFavicon'])->name('favicon');
        Route::put('/cache', [SettingsController::class, 'updateCache'])->name('cache');
        Route::post('/cache/clear', [SettingsController::class, 'clearCache'])->name('cache.clear');
    });

    // Perfil del usuario actual
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [UserController::class, 'profile'])->name('index');
        Route::put('/', [UserController::class, 'updateProfile'])->name('update');
        Route::put('/password', [UserController::class, 'updatePassword'])->name('password');
        Route::post('/avatar', [UserController::class, 'uploadAvatar'])->name('avatar');
    });
});
