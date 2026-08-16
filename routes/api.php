<?php

use App\Http\Controllers\CallController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ManagerLeadController;
use Illuminate\Support\Facades\Route;

Route::prefix('leads')->group(function () {
    Route::post('/', [LeadController::class, 'store']);
    Route::post('/{lead}/calls', [CallController::class, 'store']);
});

Route::prefix('managers')->group(function () {
    Route::get('/{manager}/leads', [ManagerLeadController::class, 'index']);
});
