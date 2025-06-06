<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Resources\UserResource;
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
