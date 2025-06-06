<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create([
        'email' => 'admin@teste.com',
        'password' => Hash::make('senhaSegura123'),
    ]);

    $this->actingAs($this->admin, 'sanctum');
});

it('permite ao admin cadastrar um funcionário válido', function () {
    Cache::shouldReceive('remember')
        ->andReturn([
            'cep' => '01001-000',
            'street' => 'Praça da Sé',
            'neighborhood' => 'Sé',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);

    $response = $this->postJson('/admin/users', [
        'name' => 'João da Silva',
        'email' => 'joao@empresa.com',
        'cpf' => '11144477735',
        'password' => 'seguro123',
        'position' => 'Analista',
        'birth_date' => '1990-05-10',
        'zipcode' => '01001000',
        'street' => 'Praça da Sé',
        'neighborhood' => 'Sé',
        'city' => 'São Paulo',
        'state' => 'SP',
        'number' => '100',
        'complement' => 'Bloco A',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.email', 'joao@empresa.com');
    $response->assertJsonPath('data.role', 'employee');

    expect(\App\Models\User::where('email', 'joao@empresa.com')->exists())->toBeTrue();
});

it('retorna erro se o CPF for inválido', function () {
    $response = $this->postJson('/admin/users', [
        'name' => 'Inválido',
        'email' => 'invalido@empresa.com',
        'cpf' => '12345678900', // CPF inválido
        'password' => '12345678',
        'position' => 'Cargo',
        'birth_date' => '2000-01-01',
        'zipcode' => '01001000',
        'street' => 'Rua XPTO',
        'city' => 'São Paulo',
        'state' => 'SP',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['cpf']);
});

it('retorna erro se o e-mail já estiver em uso', function () {
    // Cria funcionário com e-mail
    User::factory()->employee()->create([
        'email' => 'joao@empresa.com',
    ]);

    $response = $this->postJson('/admin/users', [
        'name' => 'João duplicado',
        'email' => 'joao@empresa.com', // já existente
        'cpf' => '11144477735',
        'password' => 'senha123',
        'position' => 'Analista',
        'birth_date' => '1990-05-10',
        'zipcode' => '01001000',
        'street' => 'Rua XPTO',
        'city' => 'São Paulo',
        'state' => 'SP',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

it('retorna erro se o CEP for inválido ou inexistente', function () {
    // Simula erro da API
    Cache::shouldReceive('remember')->andReturn(null);

    $response = $this->postJson('/admin/users', [
        'name' => 'Fulano',
        'email' => 'fulano@empresa.com',
        'cpf' => '11144477735',
        'password' => 'senha123',
        'position' => 'Dev',
        'birth_date' => '1992-03-15',
        'zipcode' => '00000000', // CEP inválido
        'street' => 'Rua Y',
        'city' => 'SP',
        'state' => 'SP',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['zipcode']);
});

it('retorna erro 401 se não estiver autenticado', function () {
    // Faz logout
    auth()->logout();

    $response = $this->postJson('/admin/users', [
        'name' => 'Sem login',
        'email' => 'nobody@empresa.com',
        'cpf' => '11144477735',
        'password' => 'senha123',
        'position' => 'Cargo',
        'birth_date' => '1990-01-01',
        'zipcode' => '01001000',
        'street' => 'Rua Z',
        'city' => 'São Paulo',
        'state' => 'SP',
    ]);

    $response->assertUnauthorized();
    $response->assertJson(['message' => 'Unauthenticated.']);
});
