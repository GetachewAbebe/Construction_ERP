<?php

declare(strict_types=1);

namespace App\Enums;

enum AttendanceSessionStatus: string
{
    case Present = 'present';
    case Late = 'late';
    case Absent = 'absent';
    case Leave = 'leave';

    public function label(): string
    {
        return match ($this) {
            self::Present => 'Present',
            self::Late => 'Late',
            self::Absent => 'Absent',
            self::Leave => 'On Leave',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Present => 'success',
            self::Late => 'warning',
            self::Absent => 'error',
            self::Leave => 'info',
        };
    }
}
