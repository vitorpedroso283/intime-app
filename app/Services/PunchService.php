<?php

namespace App\Services;

use App\Models\Punch;
use App\Models\User;

class PunchService
{
    /**
     * Alterna entre punch "in" e "out" com base no último registro do usuário.
     */
    public function record(User $user): Punch
    {
        $lastPunch = $user->punches()->latest('punched_at')->first();

        $type = $lastPunch?->type === 'in' ? 'out' : 'in';

        return $user->punches()->create([
            'type' => $type,
            'punched_at' => now(),
            'created_by' => null, // funcionário bateu o próprio ponto
        ]);
    }

    /**
     * Registra um punch manual com os dados fornecidos.
     *
     * Espera um array validado contendo:
     * - user_id: ID do funcionário
     * - type: 'in' ou 'out'
     * - punched_at: Data e hora do registro
     * - created_by: ID do administrador que registrou
     */ public function recordManual(array $data): Punch
    {
        return Punch::create($data);
    }
}
