<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\ClientRegistrationController;
use App\Http\Controllers\ContactSupportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\UserRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login')
);

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/about-us', AboutUsController::class)->name('about-us');
    Route::get('/client-registration', [ClientRegistrationController::class, 'index'])->name('clients.registration');
    Route::post('/client-registration', [ClientRegistrationController::class, 'store'])->name('clients.store');
    Route::get('/contact-support', [ContactSupportController::class, 'index'])->name('contact-support');
    Route::post('/contact-support', [ContactSupportController::class, 'store'])->name('contact-support.store');
    Route::get('/system-settings', SystemSettingsController::class)->name('system-settings');
    Route::put('/system-settings', [SystemSettingsController::class, 'update'])->name('system-settings.update');
    Route::resource('roles', RoleController::class)->except(['create', 'edit']);
    Route::get('/user-registration', [UserRegistrationController::class, 'index'])->name('users.index');
    Route::post('/user-registration', [UserRegistrationController::class, 'store'])->name('users.store');
    Route::put('/user-registration/{user}', [UserRegistrationController::class, 'update'])->name('users.update');
    Route::delete('/user-registration/{user}', [UserRegistrationController::class, 'destroy'])->name('users.destroy');
});
