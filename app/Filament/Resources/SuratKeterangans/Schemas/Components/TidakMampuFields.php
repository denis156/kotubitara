<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class TidakMampuFields
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
                ])
                ->helperText('Pekerjaan otomatis diambil dari data penduduk jika memilih penduduk terdaftar')
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
                ->helperText('Total penghasilan keluarga per bulan'),

            TextInput::make('data_ekonomi.jumlah_tanggungan')
                ->label('Jumlah Tanggungan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->numeric()
                ->minValue(0)
                ->validationMessages([
                    'required' => 'Jumlah tanggungan wajib diisi.',
                    'numeric' => 'Jumlah tanggungan harus berupa angka.',
                    'min' => 'Jumlah tanggungan tidak boleh kurang dari 0.',
                ])
                ->helperText('Jumlah anggota keluarga yang menjadi tanggungan'),

            Select::make('data_ekonomi.kondisi_rumah')
                ->label('Kondisi Rumah')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->options([
                    'permanen' => 'Permanen (Beton/Bata)',
                    'semi_permanen' => 'Semi Permanen',
                    'kayu' => 'Kayu',
                    'bambu' => 'Bambu',
                    'darurat' => 'Darurat/Sangat Sederhana',
                ])
                ->native(false)
                ->helperText('Kondisi bangunan tempat tinggal')
                ->columnSpanFull(),

            Select::make('data_ekonomi.kepemilikan_rumah')
                ->label('Status Kepemilikan Rumah')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->options([
                    'milik_sendiri' => 'Milik Sendiri',
                    'milik_orangtua' => 'Milik Orang Tua',
                    'menumpang' => 'Menumpang',
                    'kontrak' => 'Kontrak/Sewa',
                    'rumah_dinas' => 'Rumah Dinas',
                ])
                ->native(false)
                ->helperText('Status kepemilikan tempat tinggal')
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
                ->helperText('Upload KTP, KK, dan foto rumah sebagai bukti kondisi ekonomi. Maks 5 file @ 2MB')
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
            return $jenisSurat === JenisSuratKeterangan::TIDAK_MAMPU;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::TIDAK_MAMPU->value;
    }
}
