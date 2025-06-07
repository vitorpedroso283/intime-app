<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class ResetUserPasswordRequest extends FormRequest
{
    /**
     * Autoriza todos os administradores autenticados a fazerem esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para redefinição de senha de um usuário.
     */
    public function rules(): array
    {
        return [
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Mensagens customizadas para erros de validação.
     */
    public function messages(): array
    {
        return [
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
            'new_password' => 'nova senha',
        ];
    }
}
