<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars\Schemas\Components;

use App\Enums\JenisSuratPengantar;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class BerobatFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            TextInput::make('data_tambahan.nama_pasien')
                ->label('Nama Pasien')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Nama pasien wajib diisi.',
                    'max' => 'Nama pasien maksimal 255 karakter.',
                ])
                ->helperText('Jika berbeda dengan pemohon')
                ->columnSpanFull(),

            Select::make('data_tambahan.hubungan_dengan_pasien')
                ->label('Hubungan dengan Pasien')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->options([
                    'diri_sendiri' => 'Diri Sendiri',
                    'anak' => 'Anak',
                    'orang_tua' => 'Orang Tua',
                    'suami' => 'Suami',
                    'istri' => 'Istri',
                    'saudara' => 'Saudara',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->default('diri_sendiri')
                ->columnSpanFull(),

            Textarea::make('data_tambahan.keluhan')
                ->label('Keluhan/Kondisi Kesehatan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->rows(3)
                ->maxLength(500)
                ->validationMessages([
                    'required' => 'Keluhan/kondisi kesehatan wajib diisi.',
                    'max' => 'Keluhan maksimal 500 karakter.',
                ])
                ->helperText('Jelaskan kondisi atau keluhan kesehatan')
                ->columnSpanFull(),

            Select::make('data_tambahan.tujuan_berobat')
                ->label('Tujuan Berobat')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->options([
                    'puskesmas' => 'Puskesmas',
                    'rumah_sakit' => 'Rumah Sakit',
                    'klinik' => 'Klinik',
                    'praktek_dokter' => 'Praktek Dokter',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->searchable()
                ->columnSpanFull(),

            Select::make('data_tambahan.jenis_pengobatan')
                ->label('Jenis Pengobatan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->options([
                    'umum' => 'Pengobatan Umum',
                    'bpjs' => 'BPJS/Jaminan Kesehatan',
                    'rujukan' => 'Rujukan Rumah Sakit',
                    'gratis' => 'Berobat Gratis',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->searchable()
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
            return $jenisSurat === JenisSuratPengantar::BEROBAT;
        }

        return (string) $jenisSurat === JenisSuratPengantar::BEROBAT->value;
    }
}
