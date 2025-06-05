<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('permite logout e revoga o token atual', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token->plainTextToken,
    ])->postJson('/api/logout');

    $response->assertOk();
    $response->assertJson(['message' => 'Logout successful.']);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $token->accessToken->id,
    ]);
});

it('remove o token do banco ao fazer logout', function () {
    $user = User::factory()->create();

    $token = $user->createToken('test-token');

    $this->withHeaders([
        'Authorization' => 'Bearer ' . $token->plainTextToken,
    ])->postJson('/api/logout');

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $token->accessToken->id,
    ]);
});

it('retorna 401 ao tentar fazer logout sem autenticação', function () {
    $response = $this->postJson('/api/logout');

    $response->assertUnauthorized();
    $response->assertJson(['message' => 'Unauthenticated.']);
});
