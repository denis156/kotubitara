<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
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
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Pekerjaan wajib diisi.',
                    'max' => 'Pekerjaan maksimal 255 karakter.',
                ])
                ->columnSpanFull(),

            TextInput::make('data_ekonomi.nama_perusahaan')
                ->label('Nama Perusahaan/Instansi')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(255)
                ->helperText('Jika bekerja di perusahaan/instansi')
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

            TextInput::make('data_ekonomi.penghasilan_pertahun')
                ->label('Penghasilan Per Tahun')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->numeric()
                ->prefix('Rp')
                ->helperText('Total penghasilan per tahun'),
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
