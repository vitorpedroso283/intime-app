<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

/**
 * Regra de validação para CEPs brasileiros usando a API ViaCEP.
 *
 * Esta regra verifica se o CEP informado é válido consultando a API do ViaCEP
 * e utiliza cache para evitar múltiplas requisições. Caso o CEP não exista
 * ou a API retorne erro, a validação falha.
 *
 * O valor é armazenado no cache por 1 dia com a chave: zipcode:{cep}.
 * A URL da API deve estar definida em config/services.php → viacep.url
 */
class ValidZipCode implements ValidationRule
{
    /**
     * Executa a validação da regra.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cep = preg_replace('/[^0-9]/', '', $value); // Remove traços e pontos

        $data = Cache::remember("zipcode:$cep", now()->addDay(), function () use ($cep) {
            $url = rtrim(Config::get('services.viacep.url'), '/');
            $response = Http::get("{$url}/{$cep}/json/");

            if ($response->failed() || $response->json('erro')) {
                return null;
            }

            return [
                'cep' => $response['cep'] ?? null,
                'street' => $response['logradouro'] ?? null,
                'neighborhood' => $response['bairro'] ?? null,
                'city' => $response['localidade'] ?? null,
                'state' => $response['uf'] ?? null,
            ];
        });

        if (! $data || empty($data['cep'])) {
            $fail('O CEP informado é inválido ou não encontrado.');
        }
    }
}
