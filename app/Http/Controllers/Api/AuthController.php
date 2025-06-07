<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Services\AuthService;
use App\Traits\HandlesApiExceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use HandlesApiExceptions;

    public function __construct(protected AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->handleApi(function () use ($request) {
            $data = $this->authService->handleLogin(
                $request->validated(),
                $request->ip()
            );

            return (new LoginResource($data))->response();
        });
    }

    public function logout(Request $request): JsonResponse
    {
        return $this->handleApi(function () use ($request) {
            $this->authService->logout($request->user());

            return response()->json(['message' => 'Logout successful.']);
        });
    }
}
