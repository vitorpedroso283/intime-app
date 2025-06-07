<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PunchReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request): array
    {
        return [
            'id'             => $this->id,                               // ID do registro de ponto
            'employee_name'  => $this->employee_name,                   // Nome completo do funcionário
            'employee_position'  => $this->employee_position,                   // Cargo do funcionário
            'employee_age'   => (int) $this->employee_age,              // Idade calculada do funcionário
            'manager_name'   => $this->manager_name,                    // Nome completo do gestor (se existir)
            'punched_at'     => $this->punched_at,                      // Data e hora do punch (com segundos)
        ];
    }
}
