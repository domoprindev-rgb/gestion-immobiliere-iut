<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/setup', [AuthController::class, 'showSetup'])->name('setup');
    Route::post('/setup', [AuthController::class, 'setup'])->name('setup.store');
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/mot-de-passe-oublie', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [AuthController::class, 'sendResetLink'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reinitialiser-mot-de-passe/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reinitialiser-mot-de-passe', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/deconnexion', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', [PropertyController::class, 'dashboard'])->name('dashboard');

    Route::get('/logements', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/logements/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('/logements/{property}/modifier', [PropertyController::class, 'edit'])->middleware('role:admin,gestionnaire')->name('properties.edit');
    Route::post('/logements', [PropertyController::class, 'store'])->middleware('role:admin,gestionnaire')->name('properties.store');
    Route::put('/logements/{property}', [PropertyController::class, 'update'])->middleware('role:admin,gestionnaire')->name('properties.update');
    Route::delete('/logements/{property}', [PropertyController::class, 'destroy'])->middleware('role:admin')->name('properties.destroy');

    Route::get('/locataires', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/locataires/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
    Route::get('/locataires/{tenant}/modifier', [TenantController::class, 'edit'])->middleware('role:admin,gestionnaire')->name('tenants.edit');
    Route::post('/locataires', [TenantController::class, 'store'])->middleware('role:admin,gestionnaire')->name('tenants.store');
    Route::put('/locataires/{tenant}', [TenantController::class, 'update'])->middleware('role:admin,gestionnaire')->name('tenants.update');
    Route::delete('/locataires/{tenant}', [TenantController::class, 'destroy'])->middleware('role:admin')->name('tenants.destroy');

    Route::get('/baux', [LeaseController::class, 'index'])->name('leases.index');
    Route::post('/baux', [LeaseController::class, 'store'])->middleware('role:admin,gestionnaire')->name('leases.store');
    Route::get('/baux/{lease}/modifier', [LeaseController::class, 'edit'])->middleware('role:admin,gestionnaire')->name('leases.edit');
    Route::put('/baux/{lease}', [LeaseController::class, 'update'])->middleware('role:admin,gestionnaire')->name('leases.update');
    Route::post('/baux/{lease}/terminer', [LeaseController::class, 'terminate'])->middleware('role:admin,gestionnaire')->name('leases.terminate');

    Route::get('/loyers', [RentController::class, 'index'])->name('rents.index');
    Route::get('/paiements', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/paiements/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/paiements/{payment}/pdf', [PaymentController::class, 'downloadReceipt'])->name('payments.receipt');
    Route::post('/paiements', [PaymentController::class, 'store'])->middleware('role:admin,gestionnaire,comptable')->name('payments.store');
    Route::redirect('/quittance', '/paiements');
    Route::get('/quittance/{payment}', [PaymentController::class, 'show'])->name('receipts.show');

    Route::get('/depenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/depenses', [ExpenseController::class, 'store'])->middleware('role:admin,comptable')->name('expenses.store');
    Route::delete('/depenses/{expense}', [ExpenseController::class, 'destroy'])->middleware('role:admin')->name('expenses.destroy');

    Route::get('/interventions', [MaintenanceRequestController::class, 'index'])->name('maintenance.index');
    Route::post('/interventions', [MaintenanceRequestController::class, 'store'])->middleware('role:admin,gestionnaire')->name('maintenance.store');
    Route::put('/interventions/{maintenanceRequest}', [MaintenanceRequestController::class, 'update'])->middleware('role:admin,gestionnaire')->name('maintenance.update');

    Route::get('/rapports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/rapports/export.csv', [ReportController::class, 'export'])->name('reports.export');

    Route::middleware('role:admin')->group(function (): void {
        Route::get('/utilisateurs', [UserController::class, 'index'])->name('users.index');
        Route::post('/utilisateurs', [UserController::class, 'store'])->name('users.store');
        Route::put('/utilisateurs/{user}', [UserController::class, 'update'])->name('users.update');
    });
});
