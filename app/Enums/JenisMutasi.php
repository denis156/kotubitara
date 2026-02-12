<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum JenisMutasi: string implements HasColor, HasLabel
{
    case PINDAH_MASUK = 'pindah-masuk';
    case PINDAH_KELUAR = 'pindah-keluar';
    case PINDAH_DALAM_DESA = 'pindah-dalam-desa';

    public function getLabel(): string
    {
        return match ($this) {
            self::PINDAH_MASUK => 'Pindah Masuk',
            self::PINDAH_KELUAR => 'Pindah Keluar',
            self::PINDAH_DALAM_DESA => 'Pindah Dalam Desa',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PINDAH_MASUK => 'success',
            self::PINDAH_KELUAR => 'danger',
            self::PINDAH_DALAM_DESA => 'info',
        };
    }
}
