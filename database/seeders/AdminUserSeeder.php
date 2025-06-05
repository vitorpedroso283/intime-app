<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@intime.test'],
            [
                'name' => 'Admin Teste',
                'cpf' => '11144477735',
                'password' => Hash::make('t0atr@sado'), // pontualidade é meu forte
                'role' => UserRole::ADMIN->value,
                'position' => 'Gerente de RH',
                'birth_date' => '1990-01-01',
                'zipcode' => '01001-000',
                'street' => 'Praça da Sé',
                'neighborhood' => 'Sé',
                'city' => 'São Paulo',
                'state' => 'SP',
                'number' => '100',
                'complement' => 'Sala 10',
                'created_by' => null,
            ]
        );
    }
}
