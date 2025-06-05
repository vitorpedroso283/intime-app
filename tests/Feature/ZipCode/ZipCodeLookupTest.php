<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->viacepUrl = rtrim(Config::get('services.viacep.url'), '/');
    $this->user = User::factory()->employee()->create();
    $this->actingAs($this->user);
});

it('deve retornar os dados do endereço ao consultar um CEP válido', function () {
    Http::fake([
        "{$this->viacepUrl}/01001000/json/" => Http::response([
            'cep' => '01001-000',
            'logradouro' => 'Praça da Sé',
            'bairro' => 'Sé',
            'localidade' => 'São Paulo',
            'uf' => 'SP',
        ], 200),
    ]);

    $response = $this->getJson('/api/zipcode/01001000');

    $response->assertOk();
    $response->assertJson([
        'cep' => '01001-000',
        'street' => 'Praça da Sé',
        'neighborhood' => 'Sé',
        'city' => 'São Paulo',
        'state' => 'SP',
    ]);
});

it('deve retornar erro 404 ao consultar um CEP inválido', function () {
    Http::fake([
        "{$this->viacepUrl}/00000000/json/" => Http::response(['erro' => true], 200),
    ]);

    $response = $this->getJson('/api/zipcode/00000000');

    $response->assertNotFound();
    $response->assertJson([
        'message' => 'ZIP code not found.',
    ]);
});

it('deve retornar erro 422 para CEP com formato inválido', function () {
    $response = $this->getJson('/api/zipcode/123');

    $response->assertStatus(422);
});
