<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Traits\GeneratesCpf;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    use GeneratesCpf;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'cpf' => $this->generateFakeCpf(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('senhaSegura123'),
            'role' => fake()->randomElement(UserRole::values()), // usa os valores válidos do enum
            'position' => fake()->jobTitle(),
            'birth_date' => fake()->date('Y-m-d', '-18 years'),
            'zipcode' => '01001-000',
            'street' => fake()->streetName(),
            'neighborhood' => fake()->citySuffix(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'number' => fake()->buildingNumber(),
            'complement' => fake()->optional()->secondaryAddress(),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_by' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Define o usuário como administrador.
     *
     * Útil para testes ou seeders onde o papel precisa ser explicitamente 'admin'.
     */
    public function admin(): static
    {
        return $this->state(fn() => [
            'role' => UserRole::ADMIN->value,
        ]);
    }

    /**
     * Define o usuário como funcionário comum.
     *
     * Permite criar usuários com papel 'employee' de forma legível.
     */
    public function employee(): static
    {
        return $this->state(fn() => [
            'role' => UserRole::EMPLOYEE->value,
        ]);
    }
}
