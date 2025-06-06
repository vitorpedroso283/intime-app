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

it('permite ao admin remover um funcionário', function () {
    $employee = User::factory()->employee()->create();

    $response = $this->deleteJson("/api/admin/users/{$employee->id}");

    $response->assertOk();
    $response->assertJson([
        'message' => 'Employee deleted successfully.',
    ]);

    expect(User::find($employee->id))->toBeNull();
});

it('impede que funcionário remova outro funcionário', function () {
    $employee = User::factory()->employee()->create();
    $target = User::factory()->employee()->create();

    $token = $employee->createToken('token', [\App\Enums\TokenAbility::CLOCK_IN->value])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token);

    $response = $this->deleteJson("/api/admin/users/{$target->id}");

    $response->assertStatus(403);
});

it('marca o funcionário como removido (soft delete)', function () {
    $employee = User::factory()->employee()->create();

    $response = $this->deleteJson("/api/admin/users/{$employee->id}");

    $response->assertOk();
    $response->assertJson([
        'message' => 'Employee deleted successfully.',
    ]);

    // Deve retornar null com o método normal...
    expect(User::find($employee->id))->toBeNull();

    // ...mas ainda existe no banco via withTrashed
    $trashed = User::withTrashed()->find($employee->id);
    expect($trashed)->not->toBeNull();
    expect($trashed->deleted_at)->not->toBeNull();
});

