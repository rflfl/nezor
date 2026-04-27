<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/agenda', function () {
        return Inertia::render('Appointments');
    })->name('appointments');

    Route::get('/clientes', function () {
        return Inertia::render('Customers');
    })->name('customers');

    Route::get('/servicos', function () {
        return Inertia::render('Services');
    })->name('services');

    Route::get('/caixa', function () {
        return Inertia::render('CashRegister');
    })->name('cash-register');

    Route::get('/profissionais', function () {
        return Inertia::render('Professionals');
    })->name('professionals');

    Route::get('/lancamentos', function () {
        return Inertia::render('DailyEntries');
    })->name('daily-entries');

    Route::get('/relatorios', function () {
        return Inertia::render('Reports');
    })->name('reports');

    Route::get('/suporte', \App\Http\Controllers\SupportController::class)->name('support');

    Route::get('/settings', function () {
        return Inertia::render('Settings');
    })->name('settings');
});