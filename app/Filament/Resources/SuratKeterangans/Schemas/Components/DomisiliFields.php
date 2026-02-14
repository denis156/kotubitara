<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class DomisiliFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            TextInput::make('data_domisili.rt')
                ->label('RT')
                ->visible(fn (Get $get) => static::isVisible($get) && ! $get('penduduk_id'))
                ->hint('Opsional')
                ->maxLength(10)
                ->helperText('RT otomatis diambil dari Kartu Keluarga jika memilih penduduk terdaftar'),

            TextInput::make('data_domisili.rw')
                ->label('RW')
                ->visible(fn (Get $get) => static::isVisible($get) && ! $get('penduduk_id'))
                ->hint('Opsional')
                ->maxLength(10)
                ->helperText('RW otomatis diambil dari Kartu Keluarga jika memilih penduduk terdaftar'),

            Textarea::make('data_domisili.alamat_lengkap')
                ->label('Alamat Lengkap')
                ->visible(fn (Get $get) => static::isVisible($get) && ! $get('penduduk_id'))
                ->hint('Opsional')
                ->rows(3)
                ->maxLength(500)
                ->helperText('Alamat otomatis diambil dari Kartu Keluarga jika memilih penduduk terdaftar')
                ->columnSpanFull(),

            TextInput::make('data_domisili.lama_tinggal')
                ->label('Lama Tinggal')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(50)
                ->placeholder('Contoh: 5 tahun, 10 bulan')
                ->helperText('Berapa lama tinggal di alamat ini')
                ->columnSpanFull(),

            Select::make('data_domisili.status_tempat_tinggal')
                ->label('Status Tempat Tinggal')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->options([
                    'milik_sendiri' => 'Milik Sendiri',
                    'milik_orangtua' => 'Milik Orang Tua',
                    'milik_keluarga' => 'Milik Keluarga',
                    'menumpang' => 'Menumpang',
                    'kontrak' => 'Kontrak/Sewa',
                    'rumah_dinas' => 'Rumah Dinas',
                ])
                ->native(false)
                ->searchable()
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
                ->helperText('Upload KTP dan KK sebagai bukti domisili. Maks 5 file @ 2MB')
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
            return $jenisSurat === JenisSuratKeterangan::DOMISILI;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::DOMISILI->value;
    }
}
