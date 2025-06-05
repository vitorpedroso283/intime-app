<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Services\AuthService;
use App\Traits\HandlesApiExceptions;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
}
