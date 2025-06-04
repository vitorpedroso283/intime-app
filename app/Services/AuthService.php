<?php

namespace App\Services;

use App\Models\User;
use App\Enums\TokenAbility;

class AuthService
{
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
            ],
            'employee' => [
                TokenAbility::CLOCK_IN->value,
                TokenAbility::UPDATE_PASSWORD->value,
            ],
            default => [],
        };

        return $user->createToken('access-token', $abilities)->plainTextToken;
    }
}
