<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $token = $this->admin->createToken('test-token', \App\Enums\UserRole::ADMIN->abilities())->plainTextToken;
    $this->withHeader('Authorization', 'Bearer ' . $token);
});

it('permite ao admin listar funcionários com paginação e nome do gestor', function () {
    // Cria funcionários associados ao admin (como gestor)
    $employees = User::factory()->employee()->count(3)->create([
        'created_by' => $this->admin->id,
    ]);

    $response = $this->getJson('/api/admin/users');

    $response->assertOk();

    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'name',
                'email',
                'cpf',
                'position',
                'birth_date',
                'zipcode',
                'street',
                'neighborhood',
                'city',
                'state',
                'number',
                'complement',
                'role' => ['value', 'label'],
                'manager_name',
                'created_at',
                'updated_at',
            ],
        ],
        'links',
        'meta',
    ]);

    $response->assertJsonFragment([
        'manager_name' => $this->admin->name,
    ]);
});

it('exibe manager_name como null quando não há gestor', function () {
    $employee = User::factory()->employee()->create([
        'created_by' => null,
    ]);

    $response = $this->getJson('/api/admin/users');

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $employee->id,
        'manager_name' => null,
    ]);
});

it('impede que funcionário comum acesse a listagem de usuários', function () {
    $employee = User::factory()->employee()->create();
    $token = $employee->createToken('test-token', [\App\Enums\TokenAbility::CLOCK_IN->value])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token);

    $response = $this->getJson('/api/admin/users');

    $response->assertForbidden();
});

it('retorna a quantidade correta de registros por página', function () {
    User::factory()->employee()->count(30)->create([
        'created_by' => $this->admin->id,
    ]);

    $response = $this->getJson('/api/admin/users?per_page=10');

    $response->assertOk();
    $this->assertCount(10, $response->json('data'));
});
