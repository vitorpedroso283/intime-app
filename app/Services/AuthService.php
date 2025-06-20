<?php

namespace App\Services;

use App\Models\User;
use App\Enums\TokenAbility;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function handleLogin(array $credentials, string $ip): array
    {
        $this->checkRateLimit($ip);

        try {
            $user = $this->authenticateUser($credentials);

            Log::info('Login realizado com sucesso.', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'ip'      => $ip,
            ]);
        } catch (HttpException $e) {
            Log::warning('Tentativa de login falhou.', [
                'email' => $credentials['email'] ?? null,
                'ip'    => $ip,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        RateLimiter::clear('login:' . $ip);

        return [
            'access_token' => $this->generateTokenByRole($user),
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();

        Log::info('Logout realizado com sucesso.', [
            'user_id' => $user->id,
            'email'   => $user->email,
        ]);
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
        try {
            $role = UserRole::from($user->role);
        } catch (\ValueError $e) {
            throw new \InvalidArgumentException("Invalid role: {$user->role}");
        }

        return $user->createToken('access-token', $role->abilities())->plainTextToken;
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
            Log::warning('Rate limit excedido para login.', ['ip' => $ip]);
            throw new HttpException(429, 'Too Many Attempts.');
        }

        RateLimiter::hit($key);
    }
}
