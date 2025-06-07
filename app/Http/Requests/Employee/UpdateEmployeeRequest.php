<?php

namespace App\Http\Requests\Employee;

use App\Enums\UserRole;
use App\Rules\Cpf;
use App\Rules\ValidZipCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Autoriza todos os usuários a fazerem esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para atualização de funcionário.
     */
    public function rules(): array
    {
        $userId = $this->route('user'); // Ignora o próprio usuário nos campos únicos

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'cpf' => ['sometimes', 'required', new Cpf(), Rule::unique('users', 'cpf')->ignore($userId)],
            'password' => 'nullable|string|min:6',
            'position' => 'sometimes|required|string|max:255',
            'birth_date' => 'sometimes|required|date',
            'zipcode' => ['sometimes', 'required', new ValidZipCode()],
            'street' => 'sometimes|required|string',
            'neighborhood' => 'nullable|string',
            'role' => ['sometimes', 'required', 'string', Rule::in(UserRole::values())],
            'city' => 'sometimes|required|string',
            'state' => 'sometimes|required|string|size:2',
            'number' => 'nullable|string',
            'complement' => 'nullable|string',
        ];
    }

    /**
     * Mensagens customizadas para erros de validação.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.cpf' => 'O CPF informado não é válido.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'password.min' => 'A senha deve ter pelo menos :min caracteres.',
            'position.required' => 'O cargo é obrigatório.',
            'birth_date.required' => 'A data de nascimento é obrigatória.',
            'birth_date.date' => 'Informe uma data de nascimento válida.',
            'zipcode.required' => 'O CEP é obrigatório.',
            'street.required' => 'O logradouro é obrigatório.',
            'city.required' => 'A cidade é obrigatória.',
            'state.required' => 'O estado é obrigatório.',
            'state.size' => 'O estado deve conter exatamente 2 caracteres.',
            'role.required' => 'O campo de papel (role) é obrigatório.',
            'role.in' => 'O papel informado é inválido. Os valores permitidos são: ' .
                implode(', ', array_map(fn($role) => $role->label(), UserRole::cases())) . '.',
        ];
    }

    /**
     * Nomes amigáveis dos campos, usados nas mensagens.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'cpf' => 'CPF',
            'password' => 'senha',
            'position' => 'cargo',
            'birth_date' => 'data de nascimento',
            'zipcode' => 'CEP',
            'street' => 'logradouro',
            'neighborhood' => 'bairro',
            'city' => 'cidade',
            'state' => 'estado',
            'number' => 'número',
            'complement' => 'complemento',
            'role' => 'papel',
        ];
    }
}
