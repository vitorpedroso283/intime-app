<?php

use App\Enums\TokenAbility;
use App\Models\Punch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->employee()->create();

    $token = $this->user->createToken('clockin-token', [TokenAbility::CLOCK_IN->value])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token);
});

it('registra punch como "in" se for o primeiro do usuário', function () {
    $response = $this->postJson('/api/punches/clock-in');

    $response->assertCreated();
    $response->assertJsonPath('clock.type', 'in');

    $this->assertDatabaseHas('punches', [
        'user_id' => $this->user->id,
        'type' => 'in',
    ]);
});

it('registra punch como "out" se o último for "in"', function () {
    // Primeiro punch (in)
    Punch::create([
        'user_id' => $this->user->id,
        'type' => 'in',
        'punched_at' => now()->subMinutes(5),
    ]);

    // Segundo punch (out)
    $response = $this->postJson('/api/punches/clock-in');

    $response->assertCreated();
    $response->assertJsonPath('clock.type', 'out');

    $this->assertDatabaseHas('punches', [
        'user_id' => $this->user->id,
        'type' => 'out',
    ]);
});

it('impede registro de punch sem ability adequada', function () {
    $token = $this->user->createToken('invalid', [TokenAbility::UPDATE_PASSWORD->value])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token);

    $response = $this->postJson('/api/punches/clock-in');

    $response->assertForbidden();
});

it('retorna 401 se não estiver autenticado', function () {
    $this->flushHeaders(); // remove o token autenticado definido no beforeEach

    $response = $this->postJson('/api/punches/clock-in');

    $response->assertUnauthorized();
    $response->assertJson(['message' => 'Unauthenticated.']);
});
