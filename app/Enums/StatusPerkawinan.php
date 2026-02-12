<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusPerkawinan: string implements HasColor, HasLabel
{
    case BELUM_KAWIN = 'belum-kawin';
    case KAWIN = 'kawin';
    case CERAI_HIDUP = 'cerai-hidup';
    case CERAI_MATI = 'cerai-mati';

    public function getLabel(): string
    {
        return match ($this) {
            self::BELUM_KAWIN => 'Belum Kawin',
            self::KAWIN => 'Kawin',
            self::CERAI_HIDUP => 'Cerai Hidup',
            self::CERAI_MATI => 'Cerai Mati',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::BELUM_KAWIN => 'gray',
            self::KAWIN => 'success',
            self::CERAI_HIDUP => 'warning',
            self::CERAI_MATI => 'danger',
        };
    }
}
