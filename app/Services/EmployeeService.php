<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

        $user = User::create($data);

        Log::info('Funcionário criado.', [
            'admin_id' => $admin->id,
            'employee_id' => $user->id,
            'created_by' => $admin->id,
        ]);

        return $user;
    }

    /**
     * Atualiza um funcionário já existente com os dados validados e o admin autenticado.
     */
    public function update(User $user, array $data): User
    {
        $originalData = $user->getOriginal();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        Log::info('Funcionário atualizado.', [
            'employee_id' => $user->id,
            'before' => $originalData,
            'after' => $user->getAttributes(),
        ]);

        return $user;
    }

    /**
     * Ver dados de um funcionário
     */
    public function show(User $user): User
    {
        return $user;
    }

    /**
     * Realiza o soft delete de um funcionário.
     *
     * O registro não é removido do banco de dados,
     * apenas marcado como deletado via deleted_at.
     */
    public function delete(User $user): void
    {
        $user->delete();
        Log::info('Funcionário deletado (soft delete).', [
            'employee_id' => $user->id,
        ]);
    }

    /**
     * Retorna a lista paginada de funcionários.
     */
    public function list(array $filters = [])
    {
        $query = User::with('manager')
            ->where('role', 'employee');

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (!empty($filters['cpf'])) {
            $query->where('cpf', 'like', '%' . $filters['cpf'] . '%');
        }

        if (!empty($filters['position'])) {
            $query->where('position', 'like', '%' . $filters['position'] . '%');
        }

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (!empty($filters['birth_date_from'])) {
            $query->where('birth_date', '>=', $filters['birth_date_from']);
        }

        if (!empty($filters['birth_date_to'])) {
            $query->where('birth_date', '<=', $filters['birth_date_to']);
        }

        if (!empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        $perPage = $filters['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    /**
     * Atualiza a senha do próprio usuário autenticado.
     *
     * Verifica se a senha atual está correta antes de permitir a alteração.
     */
    public function updateOwnPassword(User $user, array $data): void
    {
        if (!Hash::check($data['current_password'], $user->password)) {

            Log::warning('Tentativa de troca de senha com senha atual incorreta.', [
                'user_id' => $user->id,
            ]);

            throw new \Symfony\Component\HttpKernel\Exception\HttpException(401, 'Current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        Log::info('Senha atualizada pelo próprio usuário.', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Redefine a senha de um usuário (feito pelo admin).
     */
    public function resetPassword(User $user, array $data): void
    {
        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        Log::info('Senha redefinida por admin.', [
            'user_id' => $user->id,
        ]);
    }
}
