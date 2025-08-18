<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Route;

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
});

require __DIR__.'/auth.php';
