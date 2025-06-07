<?php

use App\Enums\TokenAbility;
use App\Models\Punch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->manager = User::factory()->admin()->create();
    $this->employee = User::factory()->employee(['manager_id' => $this->manager->id])->create();

    $token = $this->manager->createToken('admin-token', [
        TokenAbility::VIEW_ALL_CLOCKS->value,
        TokenAbility::FILTER_CLOCKS->value,
    ])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token);
});

it('retorna relatório com registros de punch', function () {
    Punch::factory()->create([
        'user_id' => $this->employee->id,
        'type' => 'in',
        'punched_at' => now()->subDay(),
    ]);

    $response = $this->getJson('/api/punches/report');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'employee_name',
                'employee_role',
                'employee_age',
                'manager_name',
                'punched_at',
            ]
        ],
    ]);
});

it('filtra registros por período usando ability correta', function () {
    Punch::factory()->create([
        'user_id' => $this->employee->id,
        'type' => 'in',
        'punched_at' => now()->subDays(5),
    ]);

    $response = $this->getJson('/api/punches/report?from=' . now()->subDays(10)->toDateString() . '&to=' . now()->toDateString());

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});

it('filtra registros por user_id com ability correta', function () {
    Punch::factory()->create([
        'user_id' => $this->employee->id,
        'type' => 'in',
        'punched_at' => now(),
    ]);

    $response = $this->getJson('/api/punches/report?user_id=' . $this->employee->id);

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});

it('filtra registros por manager_id com ability correta', function () {
    Punch::factory()->create([
        'user_id' => $this->employee->id,
        'type' => 'in',
        'punched_at' => now(),
    ]);

    $response = $this->getJson('/api/punches/report?manager_id=' . $this->manager->id);

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});

it('filtra registros por role com ability correta', function () {
    $this->employee->update(['role' => 'Atendente de Suporte']);

    Punch::factory()->create([
        'user_id' => $this->employee->id,
        'type' => 'in',
        'punched_at' => now(),
    ]);

    $response = $this->getJson('/api/punches/report?role=suporte');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});

it('impede qualquer filtro sem ability FILTER_CLOCKS', function () {
    $token = $this->manager->createToken('no-filter', [TokenAbility::VIEW_ALL_CLOCKS->value])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token);

    $response = $this->getJson('/api/punches/report?user_id=' . $this->employee->id);

    $response->assertForbidden();
    $response->assertJson(['message' => 'Invalid ability.']);
});

it('impede acesso sem ability de visualizar', function () {
    $token = $this->manager->createToken('limited', [TokenAbility::FILTER_CLOCKS->value])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token);

    $response = $this->getJson('/api/punches/report');

    $response->assertForbidden();
    $response->assertJson(['message' => 'Invalid ability.']);
});

it('impede acesso não autenticado', function () {
    $this->flushHeaders();

    $response = $this->getJson('/api/punches/report');

    $response->assertUnauthorized();
    $response->assertJson(['message' => 'Unauthenticated.']);
});
