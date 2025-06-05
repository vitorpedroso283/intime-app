<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZipCodeLookupRequest extends FormRequest
{
    /**
     * Autoriza todos os usuários a fazerem esta requisição.
     * Como é uma consulta pública, sempre retorna true.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para consulta de CEP.
     * Garante que o CEP tenha exatamente 8 dígitos numéricos.
     */
    public function rules(): array
    {
        return [
            'cep' => ['required', 'regex:/^[0-9]{8}$/'],
        ];
    }

    /**
     * Mensagens customizadas para erros de validação.
     */
    public function messages(): array
    {
        return [
            'cep.required' => 'O campo CEP é obrigatório.',
            'cep.regex' => 'O formato do CEP deve conter exatamente 8 dígitos numéricos.',
        ];
    }

    /**
     * Nomes amigáveis dos campos, usados nas mensagens.
     */
    public function attributes(): array
    {
        return [
            'cep' => 'CEP',
        ];
    }

    /**
     * Garante que o valor do parâmetro da rota também seja validado.
     */
    public function validationData(): array
    {
        return ['cep' => $this->route('cep')];
    }
}
