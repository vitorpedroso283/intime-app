<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePunchRequest extends FormRequest
{
    /**
     * Autoriza todos os usuários a fazerem esta requisição.
     * A permissão será verificada via middleware ou policy.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para atualização de um punch.
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(['in', 'out'])],
            'punched_at' => 'required|date',
        ];
    }

    /**
     * Mensagens customizadas para erros de validação.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'O tipo de punch é obrigatório.',
            'type.in' => 'O tipo deve ser "in" ou "out".',
            'punched_at.required' => 'A data/hora do punch é obrigatória.',
            'punched_at.date' => 'Informe uma data/hora válida.',
        ];
    }

    /**
     * Nomes amigáveis dos campos, usados nas mensagens.
     */
    public function attributes(): array
    {
        return [
            'type' => 'tipo de ponto',
            'punched_at' => 'data/hora do ponto',
        ];
    }
}
