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

    $abilities = \App\Enums\UserRole::ADMIN->abilities();

    $token = $this->admin->createToken('test-token', $abilities)->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token);
});

it('permite ao admin atualizar um funcionário', function () {
    $employee = User::factory()->employee()->create([
        'name' => 'Funcionário Antigo',
        'email' => 'funcionario@empresa.com',
        'cpf' => '11144477735',
    ]);

    Cache::shouldReceive('remember')
        ->andReturn([
            'cep' => '01001-000',
            'street' => 'Rua Atualizada',
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);

    $response = $this->putJson("/api/admin/users/{$employee->id}", [
        'name' => 'Funcionário Atualizado',
        'email' => 'funcionario@empresa.com',
        'position' => 'Coordenador',
        'zipcode' => '01001000',
        'street' => 'Rua Atualizada',
        'city' => 'São Paulo',
        'state' => 'SP',
        'role' => 'employee',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.name', 'Funcionário Atualizado');
    $response->assertJsonPath('data.position', 'Coordenador');
});

it('retorna erro ao tentar atualizar com e-mail já existente', function () {
    $existing = User::factory()->employee()->create([
        'email' => 'existe@empresa.com',
    ]);

    $target = User::factory()->employee()->create([
        'email' => 'atualizar@empresa.com',
        'cpf' => '11144477735',
    ]);

    $response = $this->putJson("/api/admin/users/{$target->id}", [
        'email' => 'existe@empresa.com',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

it('retorna erro ao tentar atualizar com CPF já existente', function () {
    $existing = User::factory()->employee()->create([
        'cpf' => '11144477735',
    ]);

    $target = User::factory()->employee()->create([
        'cpf' => '22233344456',
    ]);

    $response = $this->putJson("/api/admin/users/{$target->id}", [
        'cpf' => '11144477735',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['cpf']);
});

it('retorna erro se o papel (role) for inválido', function () {
    $employee = User::factory()->employee()->create();

    $response = $this->putJson("/api/admin/users/{$employee->id}", [
        'role' => 'gerente', // inválido
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['role']);
});

it('retorna erro se o CEP atualizado for inválido', function () {
    $employee = User::factory()->employee()->create();

    Cache::shouldReceive('remember')->andReturn(null); // mocka como inválido

    $response = $this->putJson("/api/admin/users/{$employee->id}", [
        'zipcode' => '00000000',
        'street' => 'Rua Falsa',
        'city' => 'São Paulo',
        'state' => 'SP',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['zipcode']);
});
