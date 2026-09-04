<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BreachIncidentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\FormDp1Controller;
use App\Http\Controllers\FormDp2Controller;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\RopaRecordController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('compliance')->name('compliance.')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('dp1', FormDp1Controller::class)->only(['index', 'create', 'store']);
    Route::get('/dp1/{formDp1}/download', [DocumentDownloadController::class, 'dp1'])->name('dp1.download');
    Route::resource('dp2', FormDp2Controller::class)->only(['index', 'create', 'store']);
    Route::get('/dp2/{formDp2}/download', [DocumentDownloadController::class, 'dp2'])->name('dp2.download');
    Route::get('/ropa/download', [DocumentDownloadController::class, 'ropa'])->name('ropa.download');
    Route::resource('ropa', RopaRecordController::class)->only(['index', 'create', 'store']);
    Route::resource('incidents', BreachIncidentController::class)->only(['index', 'create', 'store']);
    Route::get('/incidents/{breachIncident}/download', [DocumentDownloadController::class, 'incident'])->name('incidents.download');
    Route::resource('organizations', OrganizationController::class)->only(['index', 'create', 'store']);
    Route::post('/organizations/{organization}/switch', [OrganizationController::class, 'switch'])->name('organizations.switch');
    Route::get('/privacy-policies/create', [PrivacyPolicyController::class, 'create'])->name('privacy-policies.create');
    Route::post('/privacy-policies/download', [PrivacyPolicyController::class, 'download'])->name('privacy-policies.download');
});
