<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CurrencyRateController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [WebsiteController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Websites
    Route::resource('websites', WebsiteController::class);
    
    // Payments
    Route::resource('payments', PaymentController::class);
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'viewReceipt'])->name('payments.receipt');
    Route::get('/payments/{payment}/download-receipt', [PaymentController::class, 'generateReceipt'])->name('payments.download-receipt');
    
    // Domains
    Route::resource('domains', DomainController::class);
    
    // Emails
    Route::resource('emails', EmailController::class);
    
    // Currency Rates
    Route::get('/currency-rates', [CurrencyRateController::class, 'index'])->name('currency-rates.index');
    Route::post('/currency-rates/refresh', [CurrencyRateController::class, 'refresh'])->name('currency-rates.refresh');
    Route::get('/currency-rates/api', [CurrencyRateController::class, 'getRates'])->name('currency-rates.api');
});

require __DIR__.'/auth.php';
