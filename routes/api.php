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
    // As rotas abaixo lidam com o registro de entrada e saída dos funcionários.
    // Caso o funcionário esqueça de bater o ponto, um administrador pode registrar manualmente posteriormente.
    // Cada rota exige a ability específica para garantir o controle de acesso adequado.
    Route::prefix('punches')->group(function () {
        // Registro manual de ponto - realizado por administradores com permissão para gerenciar funcionários
        Route::middleware('ability:' . TokenAbility::MANAGE_EMPLOYEES->value)->group(function () {
            Route::post('/manual', [PunchController::class, 'manualStore']);
            Route::put('/{punch}', [PunchController::class, 'update']);
            Route::delete('/{punch}', [PunchController::class, 'destroy']);
        });

        // Registro de ponto próprio (entrada/saída) - feito pelo próprio funcionário com a ability adequada
        Route::post('/clock-in', [PunchController::class, 'store'])
            ->middleware('ability:' . TokenAbility::CLOCK_IN->value);
    });
});
