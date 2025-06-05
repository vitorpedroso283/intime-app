<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ZipCodeController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rotas autenticadas com Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn() => request()->user());

    Route::get('/zipcode/{cep}', [ZipCodeController::class, 'lookup']);
});
