<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManualPunchRequest;
use App\Http\Resources\PunchResource;
use App\Services\PunchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\HandlesApiExceptions;
use Illuminate\Http\JsonResponse;

class PunchController extends Controller
{
    use HandlesApiExceptions;

    public function __construct(protected PunchService $punchService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Registra um punch (entrada ou saída) para o usuário autenticado.
     */
    public function store(): JsonResponse
    {
        return $this->handleApi(function () {
            $user = Auth::user();

            $punch = $this->punchService->record($user);

            return response()->json([
                'message' => 'Punch successfully recorded.',
                'clock' => new PunchResource($punch),
            ], 201);
        });
    }

    /**
     * Registra um punch manualmente para um funcionário específico.
     *
     * Apenas administradores com permissão apropriada devem acessar este endpoint.
     */
    public function manualStore(ManualPunchRequest $request): JsonResponse
    {
        return $this->handleApi(function () use ($request) {
            $payload = tap($request->validated(), function (&$data) {
                $data['created_by'] = Auth::id();;
            });

            $punch = $this->punchService->recordManual($payload);

            return response()->json([
                'message' => 'Manual punch successfully recorded.',
                'clock' => new PunchResource($punch),
            ], 201);
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
