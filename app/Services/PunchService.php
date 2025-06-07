<?php

namespace App\Services;

use App\Enums\PunchReportSortEnum;
use App\Models\Punch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PunchService
{
    /**
     * Alterna entre punch "in" e "out" com base no último registro do usuário.
     */
    public function record(User $user): Punch
    {
        $lastPunch = $user->punches()->latest('punched_at')->first();

        $type = $lastPunch?->type === 'in' ? 'out' : 'in';

        return $user->punches()->create([
            'type' => $type,
            'punched_at' => now(),
            'created_by' => null, // funcionário bateu o próprio ponto
        ]);
    }

    /**
     * Registra um punch manual com os dados fornecidos.
     *
     * Espera um array validado contendo:
     * - user_id: ID do funcionário
     * - type: 'in' ou 'out'
     * - punched_at: Data e hora do registro
     * - created_by: ID do administrador que registrou
     */ public function recordManual(array $data): Punch
    {
        $punch = Punch::create($data);

        Log::info('Punch registrado manualmente.', [
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'punched_at' => $data['punched_at'],
            'created_by' => $data['created_by'] ?? null,
        ]);

        return $punch;
    }

    /**
     * Atualiza os dados de um punch.
     */
    public function update(Punch $punch, array $data): Punch
    {
        $original = $punch->getOriginal();
        $punch->update($data);

        Log::info('Punch atualizado.', [
            'punch_id' => $punch->id,
            'before' => $original,
            'after' => $punch->getAttributes(),
        ]);

        return $punch;
    }

    /**
     * Gera um relatório de registros de ponto com base em filtros aplicados.
     *
     * Retorna:
     * - ID do registro
     * - Nome do funcionário
     * - Cargo
     * - Idade (calculada no SQL para MySQL ou em PHP para SQLite)
     * - Nome do gestor
     * - Data e hora completa do punch (com segundos)
     *
     * A listagem usa SQL puro via Query Builder (sem Eloquent), conforme exigido.
     * 
     * @param array $filters Filtros aplicáveis: from, to, user_id, created_by, position, sort_by, sort_dir, per_page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function generateReport(array $filters): LengthAwarePaginator
    {
        // Definição de ordenação segura usando enum
        $sortBy  = in_array($filters['sort_by'] ?? '', PunchReportSortEnum::values())
            ? $filters['sort_by']
            : PunchReportSortEnum::PUNCHED_AT->value;

        $sortDir = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $perPage = $filters['per_page'] ?? 15;

        // Detecta se o banco de dados em uso é SQLite (não suporta TIMESTAMPDIFF)
        $isSqlite = DB::getDriverName() === 'sqlite';

        // Campos que serão selecionados
        $selectFields = [
            'punches.id',
            'employees.name as employee_name',
            'employees.position as employee_position',
            'employees.birth_date', // usado para idade em SQLite
            'managers.name as manager_name',
            'punches.punched_at',
        ];

        // Se for MySQL, calcula a idade diretamente no SQL
        if (!$isSqlite) {
            $selectFields[] = DB::raw("TIMESTAMPDIFF(YEAR, employees.birth_date, CURDATE()) AS employee_age");
        }

        // Monta a query principal
        $query = DB::table('punches')
            ->join(
                'users as employees',
                fn($join) =>
                $join->on('punches.user_id', '=', 'employees.id')
            )
            ->leftJoin(
                'users as managers',
                fn($join) =>
                $join->on('employees.created_by', '=', 'managers.id')
            )
            ->select($selectFields)
            ->when(!empty($filters['from']), fn($q) =>  $q->where('punched_at', '>=', $filters['from'] . ' 00:00:00'))
            ->when(!empty($filters['to']), fn($q) =>  $q->where('punched_at', '<=', $filters['to'] . ' 23:59:59'))
            ->when(!empty($filters['user_id']),  fn($q) => $q->where('punches.user_id', intval($filters['user_id'])))
            ->when(!empty($filters['created_by']), fn($q) => $q->where('employees.created_by', intval($filters['created_by'])))
            ->when(!empty($filters['position']), fn($q) => $q->where('employees.position', 'LIKE', '%' . $filters['position'] . '%'))
            ->orderBy($sortBy, $sortDir);

        // Paginação dos resultados
        $results = $query->paginate($perPage);

        // Se for SQLite, calcula a idade manualmente com Carbon
        if ($isSqlite) {
            $results->getCollection()->transform(function ($item) {
                $item->employee_age = \Carbon\Carbon::parse($item->birth_date)->age;
                unset($item->birth_date); // remove o campo não necessário na resposta final
                return $item;
            });
        }

        return $results;
    }
}
