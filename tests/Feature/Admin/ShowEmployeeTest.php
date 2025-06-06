<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create([
        'email' => 'admin@teste.com',
        'password' => Hash::make('senhaSegura123'),
    ]);

    $token = $this->admin->createToken('test-token', \App\Enums\UserRole::ADMIN->abilities())->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token);
});

it('permite ao admin visualizar os dados de um funcionário', function () {
    $employee = User::factory()->employee()->create([
        'name' => 'Maria dos Testes',
        'email' => 'maria@empresa.com',
        'cpf' => '11144477735',
        'position' => 'Desenvolvedora',
        'birth_date' => '1995-06-10',
        'zipcode' => '01001000',
        'street' => 'Rua das Flores',
        'city' => 'São Paulo',
        'state' => 'SP',
        'role' => 'employee',
    ]);

    $response = $this->getJson("/api/admin/users/{$employee->id}");

    $response->assertOk();
    $response->assertJsonPath('data.email', 'maria@empresa.com');
    $response->assertJsonPath('data.role.value', 'employee');
    $response->assertJsonPath('data.role.label', 'Funcionário');
    $response->assertJsonPath('data.name', 'Maria dos Testes');
});

it('impede que funcionário acesse a rota de visualização de usuário', function () {
    $employee = User::factory()->employee()->create();
    $target = User::factory()->employee()->create();

    $token = $employee->createToken('token', [\App\Enums\TokenAbility::CLOCK_IN->value])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token);

    $response = $this->getJson("/api/admin/users/{$target->id}");

    $response->assertStatus(403);
});

it('retorna 404 se o funcionário não existir', function () {
    $response = $this->getJson('/api/admin/users/999999');

    $response->assertStatus(404);
});
