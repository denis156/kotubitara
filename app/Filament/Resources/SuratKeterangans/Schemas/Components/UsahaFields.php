<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class UsahaFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            TextInput::make('data_usaha.nama_usaha')
                ->label('Nama Usaha')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Nama usaha wajib diisi.',
                    'max' => 'Nama usaha maksimal 255 karakter.',
                ])
                ->columnSpanFull(),

            Select::make('data_usaha.jenis_usaha')
                ->label('Jenis Usaha')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->options([
                    'warung_toko' => 'Warung/Toko',
                    'kuliner' => 'Kuliner (Makanan/Minuman)',
                    'jasa' => 'Jasa',
                    'pertanian' => 'Pertanian',
                    'peternakan' => 'Peternakan',
                    'kerajinan' => 'Kerajinan',
                    'perdagangan' => 'Perdagangan',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->searchable()
                ->columnSpanFull(),

            Textarea::make('data_usaha.alamat_usaha')
                ->label('Alamat Usaha')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),

            TextInput::make('data_usaha.modal')
                ->label('Modal Usaha')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->numeric()
                ->prefix('Rp')
                ->helperText('Modal awal usaha'),

            TextInput::make('data_usaha.jumlah_karyawan')
                ->label('Jumlah Karyawan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->numeric()
                ->minValue(0)
                ->validationMessages([
                    'numeric' => 'Jumlah karyawan harus berupa angka.',
                    'min' => 'Jumlah karyawan tidak boleh kurang dari 0.',
                ])
                ->helperText('Jumlah karyawan yang dipekerjakan'),
        ];
    }

    protected static function isVisible(Get $get): bool
    {
        $jenisSurat = $get('jenis_surat');

        if ($jenisSurat === null) {
            return false;
        }

        if ($jenisSurat instanceof JenisSuratKeterangan) {
            return $jenisSurat === JenisSuratKeterangan::USAHA;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::USAHA->value;
    }
}
