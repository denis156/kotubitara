<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum JenisKelamin: string implements HasColor, HasLabel
{
    case LAKI_LAKI = 'laki-laki';
    case PEREMPUAN = 'perempuan';

    public function getLabel(): string
    {
        return match ($this) {
            self::LAKI_LAKI => 'Laki-laki',
            self::PEREMPUAN => 'Perempuan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::LAKI_LAKI => 'info',
            self::PEREMPUAN => 'success',
        };
    }
}
