<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\HandlesApiExceptions;

class EmployeeController extends Controller
{

    use HandlesApiExceptions;

    public function __construct(protected EmployeeService $employeeService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
