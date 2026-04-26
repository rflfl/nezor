<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ProfessionalController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\CashRegisterController;
use App\Http\Controllers\Api\DailyServiceEntryController;
use App\Http\Controllers\Api\DashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class);

    // Rotas específicas DEVEM vir antes dos apiResource para evitar conflito
    Route::get('/services/categories', [ServiceController::class, 'categories']);
    Route::get('/cash-registers/open', [CashRegisterController::class, 'open']);
    Route::get('/today-entries', [DailyServiceEntryController::class, 'index'])
        ->defaults('today', true);

    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('professionals', ProfessionalController::class);
    Route::apiResource('appointments', AppointmentController::class);
    Route::apiResource('cash-registers', CashRegisterController::class);
    Route::apiResource('daily-service-entries', DailyServiceEntryController::class);
});