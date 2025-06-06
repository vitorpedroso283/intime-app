<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ZipCodeController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rotas autenticadas com Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn() => request()->user());

    Route::get('/zipcode/{cep}', [ZipCodeController::class, 'lookup']);

    // Grupo de rotas administrativas para gerenciamento de usuários
    Route::prefix('admin')->middleware('ability:' . TokenAbility::MANAGE_EMPLOYEES->value)->group(function () {
        Route::post('/users', [EmployeeController::class, 'store']);
    });
});
