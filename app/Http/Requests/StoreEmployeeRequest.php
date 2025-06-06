<?php

namespace App\Http\Requests;

use App\Rules\Cpf;
use App\Rules\ValidZipCode;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
     * Regras de validação para criação de funcionário.
     * Garante preenchimento correto dos dados pessoais e do endereço.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => ['required', new Cpf(), 'unique:users,cpf'],
            'password' => 'required|string|min:6',
            'position' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'zipcode' => ['required', new ValidZipCode()],
            'street' => 'required|string',
            'neighborhood' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string|size:2',
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
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos :min caracteres.',
            'position.required' => 'O cargo é obrigatório.',
            'birth_date.required' => 'A data de nascimento é obrigatória.',
            'birth_date.date' => 'Informe uma data de nascimento válida.',
            'zipcode.required' => 'O CEP é obrigatório.',
            'zipcode.required' => 'O CEP é obrigatório.',
            'street.required' => 'O logradouro é obrigatório.',
            'city.required' => 'A cidade é obrigatória.',
            'state.required' => 'O estado é obrigatório.',
            'state.size' => 'O estado deve conter exatamente 2 caracteres.',
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
        ];
    }
}
