<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum HubunganKeluarga: string implements HasLabel
{
    case KEPALA_KELUARGA = 'kepala-keluarga';
    case SUAMI = 'suami';
    case ISTRI = 'istri';
    case ANAK = 'anak';
    case MENANTU = 'menantu';
    case CUCU = 'cucu';
    case ORANG_TUA = 'orang-tua';
    case MERTUA = 'mertua';
    case SAUDARA_KANDUNG = 'saudara-kandung';
    case FAMILI_LAIN = 'famili-lain';
    case PEMBANTU = 'pembantu';
    case LAINNYA = 'lainnya';

    public function getLabel(): string
    {
        return match ($this) {
            self::KEPALA_KELUARGA => 'Kepala Keluarga',
            self::SUAMI => 'Suami',
            self::ISTRI => 'Istri',
            self::ANAK => 'Anak',
            self::MENANTU => 'Menantu',
            self::CUCU => 'Cucu',
            self::ORANG_TUA => 'Orang Tua',
            self::MERTUA => 'Mertua',
            self::SAUDARA_KANDUNG => 'Saudara Kandung',
            self::FAMILI_LAIN => 'Famili Lain',
            self::PEMBANTU => 'Pembantu',
            self::LAINNYA => 'Lainnya',
        };
    }
}
