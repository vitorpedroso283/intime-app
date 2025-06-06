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
}
