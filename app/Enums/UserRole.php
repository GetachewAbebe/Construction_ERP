<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Administrator = 'Administrator';
    case HumanResourceManager = 'HumanResourceManager';
    case InventoryManager = 'InventoryManager';
    case FinancialManager = 'FinancialManager';

    public function label(): string
    {
        return match ($this) {
            self::Administrator => 'Administrator',
            self::HumanResourceManager => 'Human Resource Manager',
            self::InventoryManager => 'Inventory Manager',
            self::FinancialManager => 'Financial Manager',
        };
    }

    /**
     * Get all role values as an array.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
