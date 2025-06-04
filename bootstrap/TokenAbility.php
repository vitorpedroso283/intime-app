<?php

namespace App\Enums;

/**
 * Enum que centraliza todas as permissões (abilities) válidas utilizadas nos tokens do Sanctum.
 *
 * O uso de enums ajuda a garantir consistência e evita erros de digitação nas permissões.
 * Neste projeto, o enum também serve como documentação sobre quais permissões estão disponíveis.
 */

enum TokenAbility: string
{
    // Permissões do funcionário
    case CLOCK_IN = 'employee:clock-in';                  // Registrar um ponto
    case UPDATE_PASSWORD = 'employee:update-password';    // Atualizar a própria senha

    // Permissões do administrador
    case MANAGE_EMPLOYEES = 'admin:manage-employees';     // Operações completas de CRUD para funcionários
    case VIEW_ALL_CLOCKS = 'admin:view-all-clocks';       // Visualizar todos os registros de ponto
    case FILTER_CLOCKS = 'admin:filter-clocks';           // Filtrar registros por data
}
