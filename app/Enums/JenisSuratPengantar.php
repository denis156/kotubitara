<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum JenisSuratPengantar: string implements HasLabel
{
    case SKCK = 'skck';
    case NIKAH = 'nikah';
    case PINDAH = 'pindah';
    case KTP_KK = 'ktp-kk';
    case BEROBAT = 'berobat';
    case IZIN_KERAMAIAN = 'izin-keramaian';
    case KREDIT_BANK = 'kredit-bank';
    case LAINNYA = 'lainnya';

    public function getLabel(): string
    {
        return match ($this) {
            self::SKCK => 'Surat Pengantar SKCK',
            self::NIKAH => 'Surat Pengantar Nikah',
            self::PINDAH => 'Surat Pengantar Pindah',
            self::KTP_KK => 'Surat Pengantar KTP/KK',
            self::BEROBAT => 'Surat Pengantar Berobat',
            self::IZIN_KERAMAIAN => 'Surat Pengantar Izin Keramaian',
            self::KREDIT_BANK => 'Surat Pengantar Kredit Bank',
            self::LAINNYA => 'Surat Pengantar Lainnya',
        };
    }

    public function getPrefix(): string
    {
        return match ($this) {
            self::SKCK => 'SP/SKCK',
            self::NIKAH => 'SP/NKH',
            self::PINDAH => 'SP/PND',
            self::KTP_KK => 'SP/DOK',
            self::BEROBAT => 'SP/BRB',
            self::IZIN_KERAMAIAN => 'SP/IZN',
            self::KREDIT_BANK => 'SP/KRD',
            self::LAINNYA => 'SP/LN',
        };
    }

    public function getTujuanDefault(): ?string
    {
        return match ($this) {
            self::SKCK => 'Kepolisian Sektor (Polsek)',
            self::NIKAH => 'Kantor Urusan Agama (KUA)',
            self::PINDAH => 'Dinas Kependudukan dan Catatan Sipil',
            self::KTP_KK => 'Dinas Kependudukan dan Catatan Sipil',
            self::BEROBAT => 'Rumah Sakit/Puskesmas',
            self::IZIN_KERAMAIAN => 'Kepolisian Sektor (Polsek)',
            self::KREDIT_BANK => 'Bank/Lembaga Keuangan',
            self::LAINNYA => null,
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::SKCK => 'info',
            self::NIKAH => 'success',
            self::PINDAH => 'warning',
            self::KTP_KK => 'primary',
            self::BEROBAT => 'danger',
            self::IZIN_KERAMAIAN => 'warning',
            self::KREDIT_BANK => 'success',
            self::LAINNYA => 'gray',
        };
    }
}
