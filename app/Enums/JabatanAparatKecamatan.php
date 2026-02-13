<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum JabatanAparatKecamatan: string implements HasLabel
{
    case CAMAT = 'camat';
    case SEKRETARIS_CAMAT = 'sekretaris-camat';
    case KEPALA_SEKSI_PEMERINTAHAN = 'kepala-seksi-pemerintahan';
    case KEPALA_SEKSI_KESEJAHTERAAN_SOSIAL = 'kepala-seksi-kesejahteraan-sosial';
    case KEPALA_SEKSI_PEMBERDAYAAN_MASYARAKAT = 'kepala-seksi-pemberdayaan-masyarakat';
    case KEPALA_SEKSI_KETENTRAMAN_KETERTIBAN = 'kepala-seksi-ketentraman-ketertiban';
    case KEPALA_SEKSI_PELAYANAN_UMUM = 'kepala-seksi-pelayanan-umum';
    case KEPALA_SUB_BAGIAN_TATA_USAHA = 'kepala-sub-bagian-tata-usaha';
    case KEPALA_SUB_BAGIAN_KEUANGAN = 'kepala-sub-bagian-keuangan';
    case KEPALA_SUB_BAGIAN_PERENCANAAN = 'kepala-sub-bagian-perencanaan';
    case STAF = 'staf';

    public function getLabel(): string
    {
        return match ($this) {
            self::CAMAT => 'Camat',
            self::SEKRETARIS_CAMAT => 'Sekretaris Camat (Sekcam)',
            self::KEPALA_SEKSI_PEMERINTAHAN => 'Kepala Seksi Pemerintahan',
            self::KEPALA_SEKSI_KESEJAHTERAAN_SOSIAL => 'Kepala Seksi Kesejahteraan Sosial',
            self::KEPALA_SEKSI_PEMBERDAYAAN_MASYARAKAT => 'Kepala Seksi Pemberdayaan Masyarakat',
            self::KEPALA_SEKSI_KETENTRAMAN_KETERTIBAN => 'Kepala Seksi Ketentraman & Ketertiban',
            self::KEPALA_SEKSI_PELAYANAN_UMUM => 'Kepala Seksi Pelayanan Umum',
            self::KEPALA_SUB_BAGIAN_TATA_USAHA => 'Kepala Sub Bagian Tata Usaha',
            self::KEPALA_SUB_BAGIAN_KEUANGAN => 'Kepala Sub Bagian Keuangan',
            self::KEPALA_SUB_BAGIAN_PERENCANAAN => 'Kepala Sub Bagian Perencanaan',
            self::STAF => 'Staf',
        };
    }
}
