<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PunchController;
use App\Http\Controllers\Api\ZipCodeController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rotas autenticadas com Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/zipcode/{cep}', [ZipCodeController::class, 'lookup']);

    // Grupo de rotas administrativas para gerenciamento de usuários
    Route::prefix('admin')->middleware('ability:' . TokenAbility::MANAGE_EMPLOYEES->value)->group(function () {
        Route::post('/users', [EmployeeController::class, 'store']);
        Route::get('/users', [EmployeeController::class, 'index']);
        Route::put('/users/{user}', [EmployeeController::class, 'update']);
        Route::get('/users/{user}', [EmployeeController::class, 'show']);
        Route::delete('/users/{user}', [EmployeeController::class, 'destroy']);
    });

    // Grupo de rotas para registro de ponto (punches)
    // Acesso restrito a usuários autenticados com permissão (ability) de bater ponto (CLOCK_IN)
    Route::prefix('punches')->middleware('ability:' . TokenAbility::CLOCK_IN->value)->group(function () {
        Route::post('/clock-in', [PunchController::class, 'store']);
    });
});
