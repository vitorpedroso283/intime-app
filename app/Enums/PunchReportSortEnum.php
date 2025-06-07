<?php

namespace App\Enums;

enum PunchReportSortEnum: string
{
    case PUNCHED_AT     = 'punched_at';
    case EMPLOYEE_NAME  = 'employee_name';
    case EMPLOYEE_ROLE  = 'employee_role';
    case EMPLOYEE_AGE   = 'employee_age';
    case MANAGER_NAME   = 'manager_name';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
