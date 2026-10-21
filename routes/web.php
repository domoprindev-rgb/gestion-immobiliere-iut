<?php

use App\Http\Controllers\LeaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PropertyController::class, 'dashboard'])->name('dashboard');

Route::get('/logements', [PropertyController::class, 'index'])->name('properties.index');
Route::post('/logements', [PropertyController::class, 'store'])->name('properties.store');
Route::get('/logements/{property}', [PropertyController::class, 'show'])->name('properties.show');

Route::post('/baux', [LeaseController::class, 'store'])->name('leases.store');

Route::get('/paiements', [PaymentController::class, 'index'])->name('payments.index');
Route::post('/paiements', [PaymentController::class, 'store'])->name('payments.store');
Route::get('/paiements/{payment}', [PaymentController::class, 'show'])->name('payments.show');
Route::redirect('/quittance', '/paiements');
Route::get('/quittance/{payment}', [PaymentController::class, 'show'])->name('receipts.show');
