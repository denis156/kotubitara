<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum JenisSuratKeterangan: string implements HasLabel
{
    case DOMISILI = 'domisili';
    case USAHA = 'usaha';
    case TIDAK_MAMPU = 'tidak-mampu';
    case BELUM_MENIKAH = 'belum-menikah';
    case SUDAH_MENIKAH = 'sudah-menikah';
    case PENGHASILAN = 'penghasilan';
    case AHLI_WARIS = 'ahli-waris';
    case KEHILANGAN = 'kehilangan';
    case JANDA_DUDA = 'janda-duda';
    case KELAKUAN_BAIK = 'kelakuan-baik';

    public function getLabel(): string
    {
        return match ($this) {
            self::DOMISILI => 'Surat Keterangan Domisili (SKD)',
            self::USAHA => 'Surat Keterangan Usaha (SKU)',
            self::TIDAK_MAMPU => 'Surat Keterangan Tidak Mampu (SKTM)',
            self::BELUM_MENIKAH => 'Surat Keterangan Belum Menikah',
            self::SUDAH_MENIKAH => 'Surat Keterangan Sudah Menikah',
            self::PENGHASILAN => 'Surat Keterangan Penghasilan',
            self::AHLI_WARIS => 'Surat Keterangan Ahli Waris',
            self::KEHILANGAN => 'Surat Keterangan Kehilangan',
            self::JANDA_DUDA => 'Surat Keterangan Janda/Duda',
            self::KELAKUAN_BAIK => 'Surat Keterangan Kelakuan Baik',
        };
    }

    public function getPrefix(): string
    {
        return match ($this) {
            self::DOMISILI => 'SK/DOM',
            self::USAHA => 'SK/USH',
            self::TIDAK_MAMPU => 'SK/TM',
            self::BELUM_MENIKAH => 'SK/BM',
            self::SUDAH_MENIKAH => 'SK/SM',
            self::PENGHASILAN => 'SK/PGH',
            self::AHLI_WARIS => 'SK/AW',
            self::KEHILANGAN => 'SK/HL',
            self::JANDA_DUDA => 'SK/JD',
            self::KELAKUAN_BAIK => 'SK/KB',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DOMISILI => 'primary',
            self::USAHA => 'success',
            self::TIDAK_MAMPU => 'warning',
            self::BELUM_MENIKAH => 'info',
            self::SUDAH_MENIKAH => 'info',
            self::PENGHASILAN => 'success',
            self::AHLI_WARIS => 'danger',
            self::KEHILANGAN => 'warning',
            self::JANDA_DUDA => 'gray',
            self::KELAKUAN_BAIK => 'success',
        };
    }
}
