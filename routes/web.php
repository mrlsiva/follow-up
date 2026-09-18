<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, DashboardController, LookupController};

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('auth.login');
});
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/leads', [DashboardController::class, 'leads'])->name('leads.index');
    Route::get('/leads/create', [DashboardController::class, 'create'])->name('leads.create');
    Route::get('/leads/import', [DashboardController::class, 'importForm'])->name('leads.import');
    Route::post('/leads/import', [DashboardController::class, 'import'])->name('leads.import.submit');
    Route::post('/leads', [DashboardController::class, 'store'])->name('leads.store');
    Route::get('/leads/{lead}', [DashboardController::class, 'show'])->name('leads.show');
    Route::get('/leads/{lead}/edit', [DashboardController::class, 'edit'])->name('leads.edit');
    Route::put('/leads/{lead}', [DashboardController::class, 'update'])->name('leads.update');
    Route::delete('/leads/{lead}', [DashboardController::class, 'destroy'])->name('leads.destroy');
    Route::post('/leads/{lead}/followups', [DashboardController::class, 'followup'])->name('leads.followup');
    Route::get('/settings/sources', [LookupController::class, 'sources'])->name('settings.sources');
    Route::get('/settings/services', [LookupController::class, 'services'])->name('settings.services');
    Route::get('/settings/{type}/create', [LookupController::class, 'create'])->name('settings.lookup.create');
    Route::post('/settings/{type}', [LookupController::class, 'store'])->name('settings.lookup.store');
    Route::get('/settings/{type}/{id}/edit', [LookupController::class, 'edit'])->name('settings.lookup.edit');
    Route::put('/settings/{type}/{id}', [LookupController::class, 'update'])->name('settings.lookup.update');
    Route::delete('/settings/{type}/{id}', [LookupController::class, 'destroy'])->name('settings.lookup.destroy');
});
