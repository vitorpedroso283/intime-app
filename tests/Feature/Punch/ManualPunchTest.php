<?php

use App\Enums\TokenAbility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->employee = User::factory()->employee()->create();

    $token = $this->admin->createToken('admin-token', [TokenAbility::MANAGE_EMPLOYEES->value])->plainTextToken;
    $this->withHeader('Authorization', 'Bearer ' . $token);
});

it('permite que admin registre punch manualmente', function () {
    $data = [
        'user_id' => $this->employee->id,
        'type' => 'in',
        'punched_at' => now()->toISOString(),
    ];

    $response = $this->postJson('/api/punches/manual', $data);

    $response->assertCreated();
    $response->assertJsonPath('clock.user_id', $this->employee->id);
    $response->assertJsonPath('clock.type', 'in');

    $this->assertDatabaseHas('punches', [
        'user_id' => $this->employee->id,
        'type' => 'in',
        'created_by' => $this->admin->id,
    ]);
});

it('retorna erro de validação se dados estiverem ausentes ou inválidos', function () {
    $response = $this->postJson('/api/punches/manual', []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['user_id', 'type', 'punched_at']);
});

it('retorna 403 se usuário não tiver permissão', function () {
    $token = $this->employee->createToken('sem-permissao', [])->plainTextToken;
    $this->withHeader('Authorization', 'Bearer ' . $token);

    $response = $this->postJson('/api/punches/manual', [
        'user_id' => $this->employee->id,
        'type' => 'in',
        'punched_at' => now()->toISOString(),
    ]);

    $response->assertForbidden();
});
