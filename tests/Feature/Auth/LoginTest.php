<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'usuario@teste.com',
        'password' => Hash::make('senhaSegura123'),
    ]);
});

it('permite login com credenciais válidas', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'usuario@teste.com',
        'password' => 'senhaSegura123',
    ]);

    $response->assertOk();
    $response->assertJsonStructure([
        'access_token',
    ]);
});

it('retorna erro com credenciais inválidas', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'usuario@teste.com',
        'password' => 'senhaErrada',
    ]);

    $response->assertUnauthorized();
    $response->assertJson(['message' => 'Invalid credentials.']);
});

it('retorna erro se o e-mail for inválido', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'email_invalido',
        'password' => 'qualquerSenha',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

it('retorna erro se os campos estiverem vazios', function () {
    $response = $this->postJson('/api/login', [
        'email' => '',
        'password' => '',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email', 'password']);
});

it('bloqueia o login após muitas tentativas com falha', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/login', [
            'email' => 'usuario@teste.com',
            'password' => 'senhaIncorreta',
        ]);
    }

    $response = $this->postJson('/api/login', [
        'email' => 'usuario@teste.com',
        'password' => 'senhaIncorreta',
    ]);

    $response->assertStatus(429);
    $response->assertJson([
        'message' => 'Too Many Attempts.',
    ]);
});
