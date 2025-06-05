<?php

use App\Services\ZipCodeService;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    $this->viacepUrl = rtrim(Config::get('services.viacep.url'), '/');
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('deve armazenar no cache ao consultar um novo CEP', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(function ($key, $ttl, $callback) {
            // Simula execução do callback (requisição HTTP)
            $response = $callback();
            expect($key)->toBe('zipcode:01001000');
            expect($response)->toBeArray();
            expect($response['city'])->toBe('São Paulo');
            return true;
        });

    // Fake da resposta HTTP
    Http::fake([
        'viacep.com.br/*' => Http::response([
            'cep' => '01001-000',
            'logradouro' => 'Praça da Sé',
            'bairro' => 'Sé',
            'localidade' => 'São Paulo',
            'uf' => 'SP',
        ]),
    ]);

    $service = new ZipCodeService();
    $service->lookup('01001000');
});
