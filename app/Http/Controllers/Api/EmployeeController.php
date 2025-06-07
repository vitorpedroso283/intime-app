<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\ListEmployeesRequest;
use App\Http\Requests\Employee\ResetUserPasswordRequest;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\UpdateOwnPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use App\Traits\HandlesApiExceptions;

class EmployeeController extends Controller
{

    use HandlesApiExceptions;

    public function __construct(protected EmployeeService $employeeService) {}

    /**
     * Lista os funcionários cadastrados.
     */
    public function index(ListEmployeesRequest $request): JsonResponse
    {
        return $this->handleApi(function () use ($request) {
            $employees = $this->employeeService->list($request->validated());
            return UserResource::collection($employees)->response();
        });
    }

    /**
     * Cadastra um novo funcionário.
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        return $this->handleApi(function () use ($request) {
            $employee = $this->employeeService->create($request->validated(), auth()->user());

            return (new UserResource($employee))
                ->response()
                ->setStatusCode(201);
        });
    }

    /**
     * Exibe os dados de um funcionário.
     */
    public function show(User $user): JsonResponse
    {
        return $this->handleApi(function () use ($user) {
            return (new UserResource($this->employeeService->show($user)))->response();
        });
    }

    /**
     * Edita um funcionário.
     */
    public function update(UpdateEmployeeRequest $request, User $user): JsonResponse
    {
        return $this->handleApi(function () use ($request, $user) {
            $updated = $this->employeeService->update($user, $request->validated());

            return (new UserResource($updated))->response();
        });
    }

    /**
     * Remove um funcionário (soft delete).
     */
    public function destroy(User $user): JsonResponse
    {
        return $this->handleApi(function () use ($user) {
            $this->employeeService->delete($user);

            return response()->json([
                'message' => 'Employee deleted successfully.',
            ]);
        });
    }

    /**
     * Atualiza a senha do próprio usuário autenticado.
     */
    public function updateOwnPassword(UpdateOwnPasswordRequest $request): JsonResponse
    {
        return $this->handleApi(function () use ($request) {
            $this->employeeService->updateOwnPassword(auth()->user(), $request->validated());

            return response()->json([
                'message' => 'Password updated successfully.',
            ]);
        });
    }

    /**
     * Redefine a senha de um funcionário (feito pelo administrador).
     */
    public function resetPassword(ResetUserPasswordRequest $request, User $user): JsonResponse
    {
        return $this->handleApi(function () use ($request, $user) {
            $this->employeeService->resetPassword($user, $request->validated());

            return response()->json([
                'message' => 'Password reset successfully.',
            ]);
        });
    }
}
