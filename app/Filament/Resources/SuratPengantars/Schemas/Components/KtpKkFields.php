<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars\Schemas\Components;

use App\Enums\JenisSuratPengantar;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;

class KtpKkFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            Select::make('data_dokumen.jenis_dokumen')
                ->label('Jenis Dokumen')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->options([
                    'ktp_baru' => 'Pembuatan KTP Baru',
                    'ktp_hilang' => 'KTP Hilang',
                    'ktp_rusak' => 'KTP Rusak',
                    'kk_baru' => 'Pembuatan KK Baru',
                    'kk_hilang' => 'KK Hilang',
                    'kk_rusak' => 'KK Rusak',
                    'kk_tambah_anggota' => 'KK Tambah Anggota Keluarga',
                    'kk_kurang_anggota' => 'KK Kurang Anggota Keluarga',
                ])
                ->native(false)
                ->searchable()
                ->columnSpanFull(),

            Textarea::make('data_dokumen.keterangan')
                ->label('Keterangan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->rows(3)
                ->maxLength(500)
                ->helperText('Keterangan tambahan tentang dokumen')
                ->columnSpanFull(),
        ];
    }

    protected static function isVisible(Get $get): bool
    {
        $jenisSurat = $get('jenis_surat');

        if ($jenisSurat === null) {
            return false;
        }

        if ($jenisSurat instanceof JenisSuratPengantar) {
            return $jenisSurat === JenisSuratPengantar::KTP_KK;
        }

        return (string) $jenisSurat === JenisSuratPengantar::KTP_KK->value;
    }
}
