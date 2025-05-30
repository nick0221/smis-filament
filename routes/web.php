<?php

use App\Http\Controllers\PrintPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.app.auth.login');
});

Route::get('/print-payment/{id}', [PrintPaymentController::class, '__invoke'])->name('payment.print');
