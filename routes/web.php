<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BreachIncidentController;
use App\Http\Controllers\ComplianceCalendarController;
use App\Http\Controllers\ComplianceCatalogueController;
use App\Http\Controllers\ComplianceFormController;
use App\Http\Controllers\ComplianceReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\DpoProfileController;
use App\Http\Controllers\FormDp1Controller;
use App\Http\Controllers\FormDp2Controller;
use App\Http\Controllers\MyComplianceTaskController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PotrazSubmissionController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProjectBriefController;
use App\Http\Controllers\RopaRecordController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/services', 'services')->name('services');
Route::view('/products', 'products')->name('products');
Route::view('/contact', 'contact')->name('contact');
Route::get('/sitemap.xml', function (): Response {
    return response()
        ->view('sitemap', [
            'urls' => [
                ['location' => route('home'), 'priority' => '1.0'],
                ['location' => route('services'), 'priority' => '0.8'],
                ['location' => route('products'), 'priority' => '0.8'],
                ['location' => route('contact'), 'priority' => '0.6'],
            ],
        ])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');
Route::get('/start-project', [ProjectBriefController::class, 'create'])->name('project-brief.create');
Route::post('/start-project', [ProjectBriefController::class, 'store'])->middleware('throttle:5,1')->name('project-brief.store');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/compliance/payments/{payment}/result', [PaymentController::class, 'result'])->name('compliance.payments.result');

Route::middleware('auth')->prefix('compliance')->name('compliance.')->group(function (): void {
    Route::get('/dpo-profile', [DpoProfileController::class, 'edit'])->name('dpo-profile.edit');
    Route::put('/dpo-profile', [DpoProfileController::class, 'update'])->name('dpo-profile.update');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/calendar/export', [ComplianceCalendarController::class, 'export'])->name('calendar.export');
    Route::get('/calendar', [ComplianceCalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar', [ComplianceCalendarController::class, 'store'])->name('calendar.store');
    Route::get('/calendar/{obligation}', [ComplianceCalendarController::class, 'show'])->name('calendar.show');
    Route::put('/calendar/{obligation}', [ComplianceCalendarController::class, 'update'])->name('calendar.update');
    Route::post('/calendar/{obligation}/complete', [ComplianceCalendarController::class, 'complete'])->name('calendar.complete');
    Route::post('/calendar/{obligation}/waive', [ComplianceCalendarController::class, 'waive'])->name('calendar.waive');
    Route::post('/calendar/{obligation}/checklist', [ComplianceCalendarController::class, 'checklist'])->name('calendar.checklist');
    Route::get('/my-tasks', MyComplianceTaskController::class)->name('tasks.index');
    Route::get('/reports', ComplianceReportController::class)->name('reports.index');
    Route::get('/catalogue', [ComplianceCatalogueController::class, 'index'])->name('catalogue.index');
    Route::post('/catalogue', [ComplianceCatalogueController::class, 'store'])->name('catalogue.store');
    Route::resource('dp1', FormDp1Controller::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::get('/dp1/{formDp1}/payment', [PaymentController::class, 'showDp1'])->name('payments.dp1');
    Route::get('/dp1/{formDp1}/download', [DocumentDownloadController::class, 'dp1'])->name('dp1.download');
    Route::post('/dp1/{formDp1}/send-potraz', [PotrazSubmissionController::class, 'dp1'])->name('dp1.send-potraz');
    Route::resource('dp2', FormDp2Controller::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/dp2/{formDp2}/payment', [PaymentController::class, 'showDp2'])->name('payments.dp2');
    Route::get('/dp2/{formDp2}/download', [DocumentDownloadController::class, 'dp2'])->name('dp2.download');
    Route::post('/dp2/{formDp2}/send-potraz', [PotrazSubmissionController::class, 'dp2'])->name('dp2.send-potraz');
    Route::post('/payments/pesepay', [PaymentController::class, 'initiate'])->name('payments.initiate');
    Route::match(['get', 'post'], '/payments/{payment}/return', [PaymentController::class, 'returned'])->name('payments.return');
    Route::get('/ropa/payment', [PaymentController::class, 'showRopa'])->name('payments.ropa');
    Route::get('/ropa/download', [DocumentDownloadController::class, 'ropa'])->name('ropa.download');
    Route::resource('ropa', RopaRecordController::class)->only(['index', 'create', 'store']);
    Route::resource('forms', ComplianceFormController::class)->only(['index', 'create', 'store', 'edit', 'update'])->parameters(['forms' => 'form']);
    Route::resource('incidents', BreachIncidentController::class)->only(['index', 'create', 'store']);
    Route::get('/incidents/{breachIncident}/payment', [PaymentController::class, 'showDp3'])->name('payments.dp3');
    Route::get('/incidents/{breachIncident}/download', [DocumentDownloadController::class, 'incident'])->name('incidents.download');
    Route::post('/incidents/{breachIncident}/send-potraz', [PotrazSubmissionController::class, 'dp3'])->name('incidents.send-potraz');
    Route::resource('organizations', OrganizationController::class)->only(['index', 'create', 'store']);
    Route::post('/organizations/{organization}/switch', [OrganizationController::class, 'switch'])->name('organizations.switch');
    Route::get('/privacy-policies/create', [PrivacyPolicyController::class, 'create'])->name('privacy-policies.create');
    Route::post('/privacy-policies/download', [PrivacyPolicyController::class, 'download'])->name('privacy-policies.download');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', AdminController::class)->name('dashboard');
});
