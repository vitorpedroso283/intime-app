<?php

namespace App\Services;

use App\Models\User;
use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\RateLimiter;

class AuthService
{
    public function handleLogin(array $credentials, string $ip): array
    {
        $this->checkRateLimit($ip);

        $user = $this->authenticateUser($credentials);

        RateLimiter::clear('login:' . $ip);

        return [
            'access_token' => $this->generateTokenByRole($user),
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    /**
     * Gera um token de acesso Sanctum com permissões (abilities) baseadas no papel do usuário.
     *
     * Neste teste, os perfis e permissões são fixos, por isso utilizamos valores diretamente
     * no código com apoio de um enum. Essa abordagem é simples e suficiente para o escopo atual.
     *
     * Em um sistema maior e escalável, o ideal seria armazenar as permissões em banco de dados,
     * vinculadas a papéis ou políticas, permitindo maior flexibilidade e controle dinâmico.
     */
    public function generateTokenByRole(User $user): string
    {
        $abilities = match ($user->role) {
            'admin' => [
                TokenAbility::MANAGE_EMPLOYEES->value,
                TokenAbility::VIEW_ALL_CLOCKS->value,
                TokenAbility::FILTER_CLOCKS->value,
                TokenAbility::CLOCK_IN->value,
                TokenAbility::UPDATE_PASSWORD->value,
            ],
            'employee' => [
                TokenAbility::CLOCK_IN->value,
                TokenAbility::UPDATE_PASSWORD->value,
            ],
            default => [],
        };

        return $user->createToken('access-token', $abilities)->plainTextToken;
    }

    private function authenticateUser(array $credentials): User
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new HttpException(401, 'Invalid credentials.');
        }

        return $user;
    }

    private function checkRateLimit(string $ip): void
    {
        $key = 'login:' . $ip;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw new HttpException(429, 'Too Many Attempts.');
        }

        RateLimiter::hit($key);
    }
}
