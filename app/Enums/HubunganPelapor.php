<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum HubunganPelapor: string implements HasLabel
{
    // Untuk Kelahiran
    case AYAH = 'Ayah';
    case IBU = 'Ibu';
    case KAKEK = 'Kakek';
    case NENEK = 'Nenek';

    // Untuk Kematian & Umum
    case ANAK = 'anak';
    case ISTRI = 'istri';
    case SUAMI = 'suami';
    case ORANG_TUA = 'orang-tua';
    case SAUDARA = 'saudara';
    case KELUARGA = 'Keluarga';
    case KERABAT = 'kerabat';
    case LAINNYA = 'Lainnya';

    public function getLabel(): string
    {
        return match ($this) {
            self::AYAH => 'Ayah',
            self::IBU => 'Ibu',
            self::KAKEK => 'Kakek',
            self::NENEK => 'Nenek',
            self::ANAK => 'Anak',
            self::ISTRI => 'Istri',
            self::SUAMI => 'Suami',
            self::ORANG_TUA => 'Orang Tua',
            self::SAUDARA => 'Saudara Kandung',
            self::KELUARGA => 'Keluarga',
            self::KERABAT => 'Kerabat',
            self::LAINNYA => 'Lainnya',
        };
    }
}
