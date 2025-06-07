<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOwnPasswordRequest extends FormRequest
{
    /**
     * Autoriza todos os usuários autenticados a fazerem esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para atualização da própria senha.
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Mensagens customizadas para erros de validação.
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'A senha atual é obrigatória.',
            'new_password.required' => 'A nova senha é obrigatória.',
            'new_password.min' => 'A nova senha deve conter no mínimo :min caracteres.',
            'new_password.confirmed' => 'A confirmação da nova senha não confere.',
        ];
    }

    /**
     * Nomes amigáveis dos campos, usados nas mensagens.
     */
    public function attributes(): array
    {
        return [
            'current_password' => 'senha atual',
            'new_password' => 'nova senha',
        ];
    }
}
