<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum JabatanAparat: string implements HasLabel
{
    case KEPALA_DESA = 'kepala-desa';
    case SEKRETARIS_DESA = 'sekretaris-desa';
    case KAUR_TATA_USAHA_UMUM = 'kaur-tata-usaha-umum';
    case KAUR_KEUANGAN = 'kaur-keuangan';
    case KAUR_PERENCANAAN = 'kaur-perencanaan';
    case KASI_PEMERINTAHAN = 'kasi-pemerintahan';
    case KASI_KESEJAHTERAAN = 'kasi-kesejahteraan';
    case KASI_PELAYANAN = 'kasi-pelayanan';
    case KEPALA_DUSUN = 'kepala-dusun';

    public function getLabel(): string
    {
        return match ($this) {
            self::KEPALA_DESA => 'Kepala Desa',
            self::SEKRETARIS_DESA => 'Sekretaris Desa',
            self::KAUR_TATA_USAHA_UMUM => 'Kaur Tata Usaha & Umum',
            self::KAUR_KEUANGAN => 'Kaur Keuangan',
            self::KAUR_PERENCANAAN => 'Kaur Perencanaan',
            self::KASI_PEMERINTAHAN => 'Kasi Pemerintahan',
            self::KASI_KESEJAHTERAAN => 'Kasi Kesejahteraan',
            self::KASI_PELAYANAN => 'Kasi Pelayanan',
            self::KEPALA_DUSUN => 'Kepala Dusun',
        };
    }
}
