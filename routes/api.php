<?php

use App\Http\Controllers\Api\{AuthController, LeadController, LookupController};
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [LeadController::class, 'dashboard']);
    Route::get('/followups/today', [LeadController::class, 'today']);
    Route::post('/leads/import', [LeadController::class, 'import']);
    Route::apiResource('leads', LeadController::class)->names('api.leads');
    Route::post('/leads/{lead}/followups', [LeadController::class, 'followup']);
    Route::get('/lead-sources', [LookupController::class, 'sources'])->name('api.lead-sources.index');
    Route::post('/lead-sources', [LookupController::class, 'storeSource'])->name('api.lead-sources.store');
    Route::get('/lead-sources/{leadSource}', [LookupController::class, 'showSource'])->name('api.lead-sources.show');
    Route::put('/lead-sources/{leadSource}', [LookupController::class, 'updateSource'])->name('api.lead-sources.update');
    Route::delete('/lead-sources/{leadSource}', [LookupController::class, 'destroySource'])->name('api.lead-sources.destroy');
    Route::get('/services', [LookupController::class, 'services'])->name('api.services.index');
    Route::post('/services', [LookupController::class, 'storeService'])->name('api.services.store');
    Route::get('/services/{service}', [LookupController::class, 'showService'])->name('api.services.show');
    Route::put('/services/{service}', [LookupController::class, 'updateService'])->name('api.services.update');
    Route::delete('/services/{service}', [LookupController::class, 'destroyService'])->name('api.services.destroy');
});
