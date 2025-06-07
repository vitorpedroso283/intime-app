<?php

use App\Enums\TokenAbility;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->employee = User::factory()->employee()->create([
        'password' => Hash::make('senhaOriginal'),
    ]);
});

it('permite que um administrador redefina a senha de um usuário', function () {
    $token = $this->admin->createToken('admin-token', [TokenAbility::MANAGE_EMPLOYEES->value])->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->patchJson("/api/admin/users/{$this->employee->id}/password", [
        'new_password' => 'novaSenha123',
        'new_password_confirmation' => 'novaSenha123',
    ]);

    $response->assertOk();
    $response->assertJson([
        'message' => 'Password reset successfully.',
    ]);

    // Verifica se a senha foi atualizada
    expect(Hash::check('novaSenha123', $this->employee->fresh()->password))->toBeTrue();
});

it('retorna erro se a confirmação da nova senha estiver incorreta', function () {
    $token = $this->admin->createToken('admin-token', [TokenAbility::MANAGE_EMPLOYEES->value])->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->patchJson("/api/admin/users/{$this->employee->id}/password", [
        'new_password' => 'novaSenha123',
        'new_password_confirmation' => 'naoConfere123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['new_password']);
});

it('retorna erro se a nova senha não atingir o mínimo de caracteres', function () {
    $token = $this->admin->createToken('admin-token', [TokenAbility::MANAGE_EMPLOYEES->value])->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->patchJson("/api/admin/users/{$this->employee->id}/password", [
        'new_password' => '123',
        'new_password_confirmation' => '123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['new_password']);
});

it('impede que um funcionário comum acesse a rota de reset de senha', function () {
    $employeeToken = $this->employee->createToken('employee-token', [TokenAbility::CLOCK_IN->value])->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $employeeToken,
    ])->patchJson("/api/admin/users/{$this->admin->id}/password", [
        'new_password' => 'naoImporta123',
        'new_password_confirmation' => 'naoImporta123',
    ]);

    $response->assertForbidden();
    $response->assertJson([
        'message' => 'This action is unauthorized.',
    ]);
});
