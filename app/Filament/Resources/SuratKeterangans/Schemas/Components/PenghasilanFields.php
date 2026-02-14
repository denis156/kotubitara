<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class PenghasilanFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            TextInput::make('data_ekonomi.pekerjaan')
                ->label('Pekerjaan')
                ->visible(fn (Get $get) => static::isVisible($get) && ! $get('penduduk_id'))
                ->required(fn (Get $get) => static::isVisible($get) && ! $get('penduduk_id'))
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Pekerjaan wajib diisi.',
                    'max' => 'Pekerjaan maksimal 255 karakter.',
                ])
                ->helperText('Pekerjaan otomatis diambil dari data penduduk jika memilih penduduk terdaftar')
                ->columnSpanFull(),

            TextInput::make('data_ekonomi.jabatan')
                ->label('Jabatan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(255)
                ->helperText('Jabatan atau posisi pekerjaan')
                ->columnSpanFull(),

            TextInput::make('data_ekonomi.tempat_bekerja')
                ->label('Tempat Bekerja')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(255)
                ->helperText('Nama perusahaan/instansi tempat bekerja')
                ->columnSpanFull(),

            TextInput::make('data_ekonomi.penghasilan_perbulan')
                ->label('Penghasilan Per Bulan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->numeric()
                ->prefix('Rp')
                ->validationMessages([
                    'required' => 'Penghasilan per bulan wajib diisi.',
                    'numeric' => 'Penghasilan harus berupa angka.',
                ])
                ->helperText('Penghasilan rata-rata per bulan'),

            Select::make('data_ekonomi.sumber_penghasilan')
                ->label('Sumber Penghasilan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->options([
                    'gaji_tetap' => 'Gaji Tetap',
                    'gaji_tidak_tetap' => 'Gaji Tidak Tetap',
                    'usaha_sendiri' => 'Usaha Sendiri',
                    'wiraswasta' => 'Wiraswasta',
                    'honorarium' => 'Honorarium',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->helperText('Sumber utama penghasilan')
                ->columnSpanFull(),

            FileUpload::make('data_tambahan.dokumen_pendukung')
                ->label('Dokumen Pendukung')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->multiple()
                ->image()
                ->imageEditor()
                ->disk('public')
                ->directory('surat-keterangan/dokumen')
                ->visibility('public')
                ->maxSize(2048)
                ->maxFiles(5)
                ->hint('Opsional')
                ->helperText('Upload slip gaji atau surat keterangan penghasilan dari tempat kerja. Maks 5 file @ 2MB')
                ->columnSpanFull(),
        ];
    }

    protected static function isVisible(Get $get): bool
    {
        $jenisSurat = $get('jenis_surat');

        if ($jenisSurat === null) {
            return false;
        }

        if ($jenisSurat instanceof JenisSuratKeterangan) {
            return $jenisSurat === JenisSuratKeterangan::PENGHASILAN;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::PENGHASILAN->value;
    }
}
