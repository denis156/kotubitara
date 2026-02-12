<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasColor, HasLabel
{
    case PETUGAS_KECAMATAN = 'petugas-kecamatan';
    case PETUGAS_DESA = 'petugas-desa';

    public function getLabel(): string
    {
        return match ($this) {
            self::PETUGAS_KECAMATAN => 'Petugas Kecamatan',
            self::PETUGAS_DESA => 'Petugas Desa',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PETUGAS_KECAMATAN => 'success',
            self::PETUGAS_DESA => 'info',
        };
    }
}
