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

        // Verifica se o CPF tem exatamente 11 dígitos e não é uma sequência repetida (ex: 00000000000)
        if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            $fail('O CPF informado é inválido.');
            return;
        }

        // Inicia a verificação dos dígitos verificadores (10º e 11º dígitos do CPF)
        for ($t = 9; $t < 11; $t++) {
            $sum = 0;

            /**
             * Para calcular o dígito verificador:
             * - Multiplica-se os primeiros 9 (ou 10) dígitos por pesos decrescentes de 10 até 2 (ou 11 até 2)
             * - Soma-se os resultados
             */
            for ($i = 0; $i < $t; $i++) {
                $sum += $cpf[$i] * (($t + 1) - $i);
            }

            /**
             * O dígito verificador é calculado com base na fórmula:
             * - (10 * soma) % 11
             * - Se o resultado for maior que 9, o dígito verificador é 0
             * 
             * A fórmula ((10 * soma) % 11) % 10 faz isso de forma compacta
             */
            $digit = ((10 * $sum) % 11) % 10;

            // Compara o dígito calculado com o dígito real presente no CPF
            if ($cpf[$t] != $digit) {
                $fail('O CPF informado é inválido.');
                return;
            }
        }
    }
}
