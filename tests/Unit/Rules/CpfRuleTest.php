<?php

use App\Rules\Cpf;
use App\Traits\GeneratesCpf;

test('generateFakeCpf gera CPF válido', function () {
    $anon = new class {
        use GeneratesCpf;
    };

    $cpf = $anon->generateFakeCpf();

    expect($cpf)->toBeString()
        ->and(strlen($cpf))->toBe(11)
        ->and(preg_match('/^\d{11}$/', $cpf))->toBe(1);

    $rule = new Cpf();

    $falhou = false;
    $rule->validate('cpf', $cpf, function () use (&$falhou) {
        $falhou = true;
    });

    expect($falhou)->toBeFalse();
});

test('Cpf rule rejeita CPF inválido (dígitos iguais)', function () {
    $rule = new Cpf();
    $cpfInvalido = '11111111111';

    $falhou = false;
    $rule->validate('cpf', $cpfInvalido, function () use (&$falhou) {
        $falhou = true;
    });

    expect($falhou)->toBeTrue();
});

test('Cpf rule rejeita CPF inválido (dígito verificador)', function () {
    $rule = new Cpf();
    $cpfInvalido = '12345678900';

    $falhou = false;
    $rule->validate('cpf', $cpfInvalido, function () use (&$falhou) {
        $falhou = true;
    });

    expect($falhou)->toBeTrue();
});
