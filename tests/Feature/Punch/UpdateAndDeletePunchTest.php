<?php

use App\Enums\TokenAbility;
use App\Models\Punch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->employee = User::factory()->employee()->create();

    $this->punch = Punch::factory()->create([
        'user_id' => $this->employee->id,
        'type' => 'in',
        'punched_at' => now()->subMinutes(15),
        'created_by' => $this->admin->id,
    ]);

    $token = $this->admin->createToken('admin-token', [TokenAbility::MANAGE_EMPLOYEES->value])->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token);
});

it('permite que um administrador atualize um registro de punch', function () {
    $response = $this->putJson('/api/punches/' . $this->punch->id, [
        'type' => 'out',
        'punched_at' => now()->toDateTimeString(),
    ]);

    $response->assertOk();
    $response->assertJsonPath('clock.type', 'out');
});

it('retorna 404 ao tentar atualizar um punch inexistente', function () {
    $response = $this->putJson('/api/punches/999999', [
        'type' => 'in',
        'punched_at' => now()->toDateTimeString(),
    ]);

    $response->assertNotFound();
});

it('permite que um administrador delete um registro de punch', function () {
    $response = $this->deleteJson('/api/punches/' . $this->punch->id);

    $response->assertNoContent();
    $this->assertSoftDeleted('punches', ['id' => $this->punch->id]);
});

it('retorna 404 ao tentar deletar um punch inexistente', function () {
    $response = $this->deleteJson('/api/punches/999999');

    $response->assertNotFound();
});
