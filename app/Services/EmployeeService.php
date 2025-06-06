<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    /**
     * Cria um novo funcionário com os dados validados e o admin autenticado.
     */
    public function create(array $data, User $admin): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = UserRole::EMPLOYEE->value;
        $data['created_by'] = $admin->id;

        return User::create($data);
    }
}
