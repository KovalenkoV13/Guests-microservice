<?php

use App\Infrastructure\Http\Controllers\GuestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/guests', [GuestController::class, 'index']);

    Route::post('/guests/create', [GuestController::class, 'store']);

    Route::get('/guests/{id}', [GuestController::class, 'show']);

    Route::put('/guests/update/{id}', [GuestController::class, 'update']);

    Route::delete('/guests/delete/{id}', [GuestController::class, 'destroy']);
});