<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManualPunchRequest extends FormRequest
{
    /**
     * Autoriza todos os usuários a fazerem esta requisição.
     * A validação de permissão será tratada via middleware ou policy.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para registro manual de punch.
     * Garante os dados corretos do funcionário e do ponto.
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
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
            'user_id.required' => 'O ID do usuário é obrigatório.',
            'user_id.exists' => 'O usuário informado não foi encontrado.',
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
            'user_id' => 'usuário',
            'type' => 'tipo de punch',
            'punched_at' => 'data/hora do punch',
        ];
    }
}
