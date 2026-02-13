<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars\Schemas\Components;

use App\Enums\JenisSuratPengantar;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class IzinKeramaianFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            Select::make('data_tambahan.jenis_kegiatan')
                ->label('Jenis Kegiatan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->options([
                    'pernikahan' => 'Pernikahan/Resepsi',
                    'sunatan' => 'Sunatan/Khitanan',
                    'syukuran' => 'Syukuran',
                    'pengajian' => 'Pengajian/Tahlilan',
                    'arisan' => 'Arisan RT/RW',
                    'lomba' => 'Lomba/Pertandingan',
                    'bazaar' => 'Bazaar/Pameran',
                    'konser' => 'Konser/Pentas Seni',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->searchable()
                ->columnSpanFull(),

            TextInput::make('data_tambahan.nama_kegiatan')
                ->label('Nama Kegiatan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->columnSpanFull(),

            Textarea::make('data_tambahan.tempat_kegiatan')
                ->label('Tempat/Lokasi Kegiatan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),

            DatePicker::make('data_tambahan.tanggal_mulai')
                ->label('Tanggal Mulai Kegiatan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->native(false)
                ->displayFormat('d/m/Y'),

            DatePicker::make('data_tambahan.tanggal_selesai')
                ->label('Tanggal Selesai Kegiatan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->helperText('Jika kegiatan lebih dari 1 hari'),

            TextInput::make('data_tambahan.jumlah_peserta')
                ->label('Perkiraan Jumlah Peserta')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->numeric()
                ->minValue(1)
                ->validationMessages([
                    'numeric' => 'Jumlah peserta harus berupa angka.',
                    'min' => 'Jumlah peserta minimal 1 orang.',
                ])
                ->helperText('Perkiraan jumlah orang yang akan hadir'),

            TextInput::make('data_tambahan.penanggung_jawab')
                ->label('Penanggung Jawab Kegiatan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(255)
                ->helperText('Nama penanggung jawab (jika berbeda dengan pemohon)')
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
            return $jenisSurat === JenisSuratPengantar::IZIN_KERAMAIAN;
        }

        return (string) $jenisSurat === JenisSuratPengantar::IZIN_KERAMAIAN->value;
    }
}
