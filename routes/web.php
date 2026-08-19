<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecoveryController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WhatsappSettingController;
use App\Http\Controllers\OpdVisitController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\IpdController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\PrintSettingController;
use App\Http\Controllers\PathologyController;
use App\Http\Controllers\BackupController; // <-- ADDED THIS IMPORT

// ===== Public routes (no login required) =====
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/recover', [RecoveryController::class, 'showFindAccount'])->name('recovery.find');
Route::post('/recover', [RecoveryController::class, 'findAccount'])->name('recovery.find.submit');
Route::post('/recover/verify', [RecoveryController::class, 'checkAnswer'])->name('recovery.verify');
Route::post('/recover/reset', [RecoveryController::class, 'resetAccount'])->name('recovery.reset');
Route::post('/patients/quick-store', [PatientController::class, 'quickStore'])->name('patients.quick-store');
Route::get('/opd/doctor-fee/{doctor}', [OpdVisitController::class, 'doctorFee'])->name('opd.doctor-fee');

// ===== Protected routes (must be logged in) =====
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Resources
    Route::resource('patients', PatientController::class);
    Route::resource('doctors', DoctorController::class);
    Route::resource('opd', OpdVisitController::class);
    Route::resource('appointments', AppointmentController::class);

    // Profile & Password
    Route::get('/profile/password', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [ProfileController::class, 'update'])->name('profile.update');

    // WhatsApp Settings
    Route::get('/whatsapp/settings', [WhatsappSettingController::class, 'edit'])->name('whatsapp.settings');
    Route::put('/whatsapp/settings', [WhatsappSettingController::class, 'update'])->name('whatsapp.settings.update');
    Route::post('/whatsapp/mark-sent/{patient}', [WhatsappSettingController::class, 'markSent'])->name('whatsapp.mark-sent');

    // Appointments Extra Actions
    Route::put('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::put('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::get('/appointments/{appointment}/details', [AppointmentController::class, 'details'])->name('appointments.details');

    // ===== Revenue Analysis, Export & Import =====
    Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue.index');
    Route::get('/revenue/export', [RevenueController::class, 'export'])->name('revenue.export');
    Route::post('/revenue/import', [RevenueController::class, 'import'])->name('revenue.import');

    // ===== Billing & Invoices =====
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/invoice/{id}', [BillingController::class, 'invoice'])->name('billing.invoice');
    Route::get('/billing/settings', [BillingController::class, 'settings'])->name('billing.settings');
    Route::post('/billing/settings', [BillingController::class, 'updateSettings'])->name('billing.settings.update');

    // ===== PRINT LAYOUT SETTINGS (Securely inside auth) =====
    Route::get('/settings/print-layout', [PrintSettingController::class, 'edit'])->name('settings.print_layout');
    Route::post('/settings/print-layout', [PrintSettingController::class, 'update'])->name('settings.print_layout.update');

    // ===== IPD Module =====
    Route::get('/ipd-dashboard', [IpdController::class, 'dashboard'])->name('ipd.dashboard');
    Route::get('/ipd/create', [IpdController::class, 'create'])->name('ipd.create');
    Route::post('/ipd/store', [IpdController::class, 'store'])->name('ipd.store');
    Route::get('/ipd/{ipdAdmission}/edit', [IpdController::class, 'edit'])->name('ipd.edit');
    Route::put('/ipd/{ipdAdmission}', [IpdController::class, 'update'])->name('ipd.update');
    
    // BED ALLOCATION ROUTES
    Route::get('/ipd/{ipdAdmission}/allocate', [IpdController::class, 'allocate'])->name('ipd.allocate');
    Route::put('/ipd/{ipdAdmission}/allocate', [IpdController::class, 'allocateUpdate'])->name('ipd.allocate.update');

    // IPD DETAILS SHOW PAGE
    Route::get('/ipd/{ipdAdmission}/show', [IpdController::class, 'show'])->name('ipd.show');

    Route::delete('/ipd/{ipdAdmission}', [IpdController::class, 'destroy'])->name('ipd.destroy');

    // PATIENT DETAILS VIEW FROM IPD
    Route::get('/ipd/patient/{patient}', [IpdController::class, 'showPatient'])->name('ipd.patient.show');

    // ===== Bed Management =====
    Route::get('/ipd/beds', [BedController::class, 'index'])->name('ipd.beds.index');
    Route::get('/ipd/beds/create', [BedController::class, 'create'])->name('ipd.beds.create');
    Route::post('/ipd/beds', [BedController::class, 'store'])->name('ipd.beds.store');
    Route::get('/ipd/beds/{bed}/edit', [BedController::class, 'edit'])->name('ipd.beds.edit');
    Route::put('/ipd/beds/{bed}', [BedController::class, 'update'])->name('ipd.beds.update');
    Route::delete('/ipd/beds/{bed}', [BedController::class, 'destroy'])->name('ipd.beds.destroy');

    // ===== Pathology Module =====
    Route::get('/pathology-dashboard', [PathologyController::class, 'dashboard'])->name('pathology.dashboard');
    Route::post('/pathology/store', [PathologyController::class, 'store'])->name('pathology.store');
    Route::get('/pathology/{labTest}/edit', [PathologyController::class, 'edit'])->name('pathology.edit');
    Route::put('/pathology/{labTest}', [PathologyController::class, 'update'])->name('pathology.update');
    Route::get('/pathology/{id}/report', [PathologyController::class, 'report'])->name('pathology.report');

    // ===== BACKUP & RESTORE MODULE =====
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup/settings', [BackupController::class, 'updateSettings'])->name('backup.settings');
    Route::post('/backup/run', [BackupController::class, 'runNow'])->name('backup.run');
    Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
    Route::post('/backup/import', [BackupController::class, 'import'])->name('backup.import');

    // ===== Admin & User Management (Super Admin only) =====
    Route::middleware('super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::put('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });
});