<?php

use App\Enums\TokenAbility;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->employee()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('senhaAntiga123'),
    ]);
});

it('atualiza a senha corretamente quando senha atual e confirmação são válidas', function () {
    $token = $this->user->createToken('test-token', [TokenAbility::UPDATE_PASSWORD->value])->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->patchJson('/api/me/password', [
        'current_password' => 'senhaAntiga123',
        'new_password' => 'novaSenha123',
        'new_password_confirmation' => 'novaSenha123',
    ]);

    $response->assertOk();
    $response->assertJson([
        'message' => 'Password updated successfully.',
    ]);

    // Verifica se a senha foi realmente atualizada
    expect(Hash::check('novaSenha123', $this->user->fresh()->password))->toBeTrue();
});

it('retorna erro se a senha atual estiver incorreta', function () {
    $token = $this->user->createToken('test-token', [TokenAbility::UPDATE_PASSWORD->value])->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->patchJson('/api/me/password', [
        'current_password' => 'senhaErrada',
        'new_password' => 'novaSenha123',
        'new_password_confirmation' => 'novaSenha123',
    ]);

    $response->assertStatus(401);
    $response->assertJson([
        'message' => 'Current password is incorrect.',
    ]);
});

it('retorna erro se a nova senha e confirmação forem diferentes', function () {
    $token = $this->user->createToken('test-token', [TokenAbility::UPDATE_PASSWORD->value])->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->patchJson('/api/me/password', [
        'current_password' => 'senhaAntiga123',
        'new_password' => 'novaSenha123',
        'new_password_confirmation' => 'diferente123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['new_password']);
});

it('retorna erro se o token não tiver a ability necessária', function () {
    $token = $this->user->createToken('test-token', [])->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->patchJson('/api/me/password', [
        'current_password' => 'senhaAntiga123',
        'new_password' => 'novaSenha123',
        'new_password_confirmation' => 'novaSenha123',
    ]);

    $response->assertForbidden();
    $response->assertJson([
        'message' => 'This action is unauthorized.',
    ]);
});
