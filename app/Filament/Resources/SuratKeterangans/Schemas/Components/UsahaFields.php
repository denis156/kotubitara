<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\FileUpload;
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
                ->required(fn (Get $get) => static::isVisible($get))
                ->options([
                    'warung_kelontong' => 'Warung Kelontong',
                    'toko_sembako' => 'Toko Sembako',
                    'kuliner' => 'Kuliner (Makanan/Minuman)',
                    'bengkel' => 'Bengkel',
                    'penjahit' => 'Penjahit/Konveksi',
                    'salon_barbershop' => 'Salon/Barbershop',
                    'jasa_service' => 'Jasa Service',
                    'pertanian' => 'Pertanian',
                    'peternakan' => 'Peternakan',
                    'kerajinan' => 'Kerajinan',
                    'perdagangan' => 'Perdagangan',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->searchable()
                ->validationMessages([
                    'required' => 'Jenis usaha wajib dipilih.',
                ])
                ->columnSpanFull(),

            Textarea::make('data_usaha.alamat_usaha')
                ->label('Alamat Usaha')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->rows(2)
                ->maxLength(500)
                ->validationMessages([
                    'required' => 'Alamat usaha wajib diisi.',
                ])
                ->columnSpanFull(),

            TextInput::make('data_usaha.lama_usaha')
                ->label('Lama Usaha')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(50)
                ->placeholder('Contoh: 3 tahun, 6 bulan')
                ->helperText('Sudah berapa lama menjalankan usaha ini'),

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
                ->helperText('Upload foto tempat usaha dan NPWP (jika ada). Maks 5 file @ 2MB')
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
            return $jenisSurat === JenisSuratKeterangan::USAHA;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::USAHA->value;
    }
}
