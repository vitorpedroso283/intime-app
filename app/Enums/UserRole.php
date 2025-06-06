<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case EMPLOYEE = 'employee';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::values());
    }

    public function abilities(): array
    {
        return match ($this) {
            self::ADMIN => [
                TokenAbility::MANAGE_EMPLOYEES->value,
                TokenAbility::VIEW_ALL_CLOCKS->value,
                TokenAbility::FILTER_CLOCKS->value,
                TokenAbility::CLOCK_IN->value,
                TokenAbility::UPDATE_PASSWORD->value,
            ],
            self::EMPLOYEE => [
                TokenAbility::CLOCK_IN->value,
                TokenAbility::UPDATE_PASSWORD->value,
            ],
        };
    }

    public static function labels(): array
    {
        return [
            self::ADMIN->value => self::ADMIN->label(),
            self::EMPLOYEE->value => self::EMPLOYEE->label(),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::EMPLOYEE => 'Funcionário',
        };
    }
}
