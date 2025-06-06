<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Regra customizada para validação de CPF (Cadastro de Pessoa Física - Brasil).
 *
 * Utilizamos uma regra própria para garantir segurança e controle total,
 * já que não existe uma biblioteca oficial do Laravel para CPF e muitas libs
 * externas não são mantidas ou trazem dependências desnecessárias.
 *
 * A validação é baseada no algoritmo oficial do CPF, validando os dígitos
 * verificadores com base em uma matemática exata.
 */
class Cpf implements ValidationRule
{
    /**
     * Executa a validação do CPF.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cpf = preg_replace('/[^0-9]/', '', $value); // Remove pontos e traços

        if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            $fail('O CPF informado é inválido.');
            return;
        }

        // Validação dos dois dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $sum = 0;

            for ($i = 0; $i < $t; $i++) {
                $sum += $cpf[$i] * (($t + 1) - $i);
            }

            $digit = ((10 * $sum) % 11) % 10;

            if ($cpf[$t] != $digit) {
                $fail('O CPF informado é inválido.');
                return;
            }
        }
    }
}
