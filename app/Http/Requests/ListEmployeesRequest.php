<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListEmployeesRequest extends FormRequest
{
    /**
     * Autoriza todos os usuários a fazerem esta requisição.
     * A validação de permissão pode ser implementada depois via Policy.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para listagem de funcionários.
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email'],
            'cpf' => ['sometimes', 'string'],
            'position' => ['sometimes', 'string'],
            'role' => ['sometimes', 'string', Rule::in(UserRole::values())],
            'birth_date_from' => ['sometimes', 'date'],
            'birth_date_to' => ['sometimes', 'date', 'after_or_equal:birth_date_from'],
            'created_by' => ['sometimes', 'integer', 'exists:users,id'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    /**
     * Mensagens customizadas para erros de validação.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'O nome deve ser um texto.',
            'email.email' => 'Informe um e-mail válido.',
            'cpf.string' => 'O CPF deve ser um texto.',
            'position.string' => 'O cargo deve ser um texto.',
            'role.string' => 'O papel deve ser um texto.',
            'role.in' => 'O papel informado é inválido. Os valores permitidos são: ' .
                implode(', ', array_map(fn($role) => $role->label(), UserRole::cases())) . '.',
            'birth_date_from.date' => 'A data inicial de nascimento deve ser uma data válida.',
            'birth_date_to.date' => 'A data final de nascimento deve ser uma data válida.',
            'birth_date_to.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
            'created_by.integer' => 'O campo gestor deve ser um número inteiro.',
            'created_by.exists' => 'O gestor informado não foi encontrado.',
            'per_page.integer' => 'O campo per_page deve ser um número.',
            'per_page.min' => 'O valor mínimo para per_page é 1.',
            'per_page.max' => 'O valor máximo para per_page é 100.',
            'page.integer' => 'A página deve ser um número.',
            'page.min' => 'O valor mínimo para página é 1.',
        ];
    }

    /**
     * Nomes amigáveis dos campos.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'cpf' => 'CPF',
            'position' => 'cargo',
            'role' => 'papel',
            'birth_date_from' => 'data de nascimento inicial',
            'birth_date_to' => 'data de nascimento final',
            'created_by' => 'gestor',
            'per_page' => 'quantidade por página',
            'page' => 'página',
        ];
    }
}
