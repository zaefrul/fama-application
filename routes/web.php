<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Exporter\ApplicationController as ExporterApplicationController;
use App\Http\Controllers\Exporter\CompanyController as ExporterCompanyController;
use App\Http\Controllers\Exporter\DashboardController as ExporterDashboardController;
use App\Http\Controllers\Exporter\QrController as ExporterQrController;
use App\Http\Controllers\Fama\ApplicationController as FamaApplicationController;
use App\Http\Controllers\Fama\CompanyController as FamaCompanyController;
use App\Http\Controllers\Fama\DashboardController as FamaDashboardController;
use App\Http\Controllers\Fama\QrController as FamaQrController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\QrImageController;
use App\Http\Controllers\TraceController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/api/health', HealthController::class);
Route::get('/api/qr', QrImageController::class);
Route::get('/api/integrations/dagangnet/company/{identifier}', [IntegrationController::class, 'company']);
Route::get('/api/integrations/ifama/staff/{identifier}', [IntegrationController::class, 'staff']);
Route::get('/api/public/trace/{qrCode}', [TraceController::class, 'api']);
Route::get('/trace/{qrCode}', [TraceController::class, 'show'])->name('trace.show');

Route::middleware('guest')->group(function () {
    Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/auth/register/exporter', [AuthController::class, 'showRegisterExporter'])->name('register.exporter');
    Route::post('/auth/register/exporter', [AuthController::class, 'registerExporter']);
    Route::get('/auth/register/fama', [AuthController::class, 'showRegisterFama'])->name('register.fama');
    Route::post('/auth/register/fama', [AuthController::class, 'registerFama']);
    Route::get('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
});

Route::post('/api/auth/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/auth/logout', [AuthController::class, 'logout']);

Route::middleware(['auth', 'role:EXPORTER'])->group(function () {
    Route::get('/exporter', ExporterDashboardController::class)->name('exporter.dashboard');
    Route::get('/exporter/company', [ExporterCompanyController::class, 'show'])->name('exporter.company');
    Route::post('/exporter/company', [ExporterCompanyController::class, 'update']);
    Route::get('/exporter/company/produce', [ExporterCompanyController::class, 'produce'])->name('exporter.produce');
    Route::post('/exporter/company/produce', [ExporterCompanyController::class, 'addProduce']);
    Route::post('/exporter/company/produce/delete', [ExporterCompanyController::class, 'removeProduce']);
    Route::get('/exporter/company/certificates', [ExporterCompanyController::class, 'certificates'])->name('exporter.certificates');
    Route::post('/exporter/company/certificates', [ExporterCompanyController::class, 'addCertificate']);
    Route::post('/exporter/company/certificates/delete', [ExporterCompanyController::class, 'deleteCertificate']);
    Route::get('/exporter/company/gallery', [ExporterCompanyController::class, 'gallery'])->name('exporter.gallery');
    Route::post('/exporter/company/gallery', [ExporterCompanyController::class, 'addGallery']);
    Route::post('/exporter/company/gallery/delete', [ExporterCompanyController::class, 'deleteGallery']);
    Route::get('/exporter/applications', [ExporterApplicationController::class, 'index'])->name('exporter.applications');
    Route::get('/exporter/applications/new', [ExporterApplicationController::class, 'create'])->name('exporter.applications.create');
    Route::post('/exporter/applications', [ExporterApplicationController::class, 'store']);
    Route::get('/exporter/applications/{id}', [ExporterApplicationController::class, 'show'])->name('exporter.applications.show');
    Route::post('/exporter/applications/{id}', [ExporterApplicationController::class, 'update']);
    Route::post('/exporter/applications/{id}/submit', [ExporterApplicationController::class, 'submit'])->name('exporter.applications.submit');
    Route::post('/exporter/applications/{id}/qr', [ExporterApplicationController::class, 'generateQr']);
    Route::get('/exporter/qr', [ExporterQrController::class, 'index'])->name('exporter.qr');
    Route::get('/exporter/qr/{id}', [ExporterQrController::class, 'show'])->name('exporter.qr.show');
    Route::get('/exporter/qr/{id}/download', [ExporterQrController::class, 'downloadPage'])->name('exporter.qr.download');
    Route::get('/exporter/audit', [ExporterQrController::class, 'audit'])->name('exporter.audit');
});

Route::middleware(['auth', 'role:EXPORTER,FAMA_OFFICER'])->group(function () {
    Route::get('/api/exporter/qr/{id}/download', [ExporterQrController::class, 'download'])->name('qr.download');
});

Route::middleware(['auth', 'role:FAMA_OFFICER'])->group(function () {
    Route::get('/fama', FamaDashboardController::class)->name('fama.dashboard');
    Route::get('/fama/menu', [FamaDashboardController::class, 'menu'])->name('fama.menu');
    Route::get('/fama/audit', [FamaDashboardController::class, 'audit'])->name('fama.audit');
    Route::get('/fama/applications', [FamaApplicationController::class, 'index'])->name('fama.applications');
    Route::get('/fama/applications/{id}', [FamaApplicationController::class, 'show'])->name('fama.applications.show');
    Route::post('/fama/applications/{id}/approve', [FamaApplicationController::class, 'approve'])->name('fama.applications.approve');
    Route::post('/fama/applications/{id}/reject', [FamaApplicationController::class, 'reject'])->name('fama.applications.reject');
    Route::get('/fama/companies', [FamaCompanyController::class, 'index'])->name('fama.companies');
    Route::get('/fama/companies/new', [FamaCompanyController::class, 'create'])->name('fama.companies.create');
    Route::post('/fama/companies', [FamaCompanyController::class, 'store']);
    Route::get('/fama/companies/{id}', [FamaCompanyController::class, 'show'])->name('fama.companies.show');
    Route::post('/fama/companies/{id}', [FamaCompanyController::class, 'update']);
    Route::get('/fama/companies/{id}/qr/new', [FamaCompanyController::class, 'createQr'])->name('fama.companies.qr.create');
    Route::post('/fama/companies/{id}/qr', [FamaCompanyController::class, 'storeQr']);
    Route::get('/fama/companies/{id}/qr/{applicationId}', [FamaCompanyController::class, 'editQr'])->name('fama.companies.qr.edit');
    Route::post('/fama/companies/{id}/qr/{applicationId}', [FamaCompanyController::class, 'updateQr']);
    Route::post('/fama/companies/{id}/certificates', [FamaCompanyController::class, 'addCertificate']);
    Route::post('/fama/companies/{id}/certificates/delete', [FamaCompanyController::class, 'deleteCertificate']);
    Route::get('/fama/qr', FamaQrController::class)->name('fama.qr');
});
