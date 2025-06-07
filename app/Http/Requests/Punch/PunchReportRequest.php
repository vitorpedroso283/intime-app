<?php

namespace App\Http\Requests\Punch;

use App\Enums\PunchReportSortEnum;
use App\Enums\TokenAbility;
use Illuminate\Foundation\Http\FormRequest;

class PunchReportRequest extends FormRequest
{
    /**
     * Autoriza todos os usuários a fazerem esta requisição.
     * A validação de permissão pode ser implementada depois via Policy.
     */
    public function authorize(): bool
    {
        $filters = $this->only(['from', 'to', 'user_id', 'created_by', 'position']);
        $hasFilters = collect($filters)->filter()->isNotEmpty();

        if ($hasFilters && !auth()->user()?->tokenCan(TokenAbility::FILTER_CLOCKS->value)) {
            return false;
        }

        return true;
    }

    /**
     * Define as regras de validação para geração do relatório de ponto.
     */
    public function rules(): array
    {
        return [
            'from'        => 'nullable|date',
            'to'          => 'nullable|date|after_or_equal:from',
            'user_id'     => 'nullable|exists:users,id',
            'created_by'  => 'nullable|exists:users,id',
            'position'        => 'nullable|string|max:255',
            'sort_by'     => 'nullable|string|in:' . implode(',', PunchReportSortEnum::values()),
            'sort_dir'    => 'nullable|string|in:asc,desc',
            'per_page'    => 'nullable|integer|min:1|max:100',
            'page'        => 'nullable|integer|min:1',
        ];
    }

    /**
     * Mensagens personalizadas para os erros de validação.
     */
    public function messages(): array
    {
        return [
            'from.date'            => 'A data inicial deve ser uma data válida.',
            'to.date'              => 'A data final deve ser uma data válida.',
            'to.after_or_equal'    => 'A data final deve ser igual ou posterior à data inicial.',
            'user_id.exists'       => 'O funcionário selecionado é inválido.',
            'created_by.exists'    => 'O gestor selecionado é inválido.',
            'role.string'          => 'O cargo deve ser um texto válido.',
            'sort_by.in'           => 'O campo de ordenação selecionado é inválido.',
            'sort_dir.in'          => 'A direção de ordenação deve ser asc ou desc.',
            'per_page.integer'     => 'O número de itens por página deve ser um número inteiro.',
            'per_page.min'         => 'O número de itens por página deve ser no mínimo 1.',
            'per_page.max'         => 'O número de itens por página não pode exceder 100.',
            'page.integer'         => 'A página deve ser um número inteiro.',
            'page.min'             => 'A página deve ser no mínimo 1.',
        ];
    }

    /**
     * Nomes amigáveis para os atributos.
     */
    public function attributes(): array
    {
        return [
            'from'       => 'data inicial',
            'to'         => 'data final',
            'user_id'    => 'funcionário',
            'created_by' => 'gestor',
            'role'       => 'cargo',
            'sort_by'    => 'ordenar por',
            'sort_dir'   => 'direção da ordenação',
            'per_page'   => 'itens por página',
            'page'       => 'página',
        ];
    }
}
