<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;

class KematianFields
{
    public static function make(): array
    {
        return [
            Select::make('data_kematian.kematian_id')
                ->label('Data Kematian')
                ->relationship('kematian', 'id')
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->penduduk->nama_lengkap} - {$record->tanggal_meninggal->format('d/m/Y')}")
                ->searchable(['penduduk.nama_lengkap'])
                ->preload()
                ->hint('Opsional')
                ->helperText('Pilih dari data kematian yang sudah ada.')
                ->visible(fn (Get $get): bool => self::isVisible($get))
                ->columnSpanFull(),

            DatePicker::make('data_kematian.tanggal_meninggal')
                ->label('Tanggal Meninggal')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                ->hint('Opsional')
                ->helperText('Tanggal penduduk meninggal dunia.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TimePicker::make('data_kematian.waktu_meninggal')
                ->label('Waktu Meninggal')
                ->native(false)
                ->seconds(false)
                ->hint('Opsional')
                ->helperText('Jam dan menit saat meninggal.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kematian.tempat_meninggal')
                ->label('Tempat Meninggal')
                ->maxLength(255)
                ->hint('Opsional')
                ->helperText('Lokasi atau tempat penduduk meninggal (cth: Rumah, Rumah Sakit).')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kematian.sebab_kematian')
                ->label('Sebab Kematian')
                ->maxLength(255)
                ->hint('Opsional')
                ->helperText('Penyebab kematian (cth: Sakit, Kecelakaan, Usia Lanjut).')
                ->visible(fn (Get $get): bool => self::isVisible($get))
                ->columnSpanFull(),

            TextInput::make('data_kematian.tempat_pemakaman')
                ->label('Tempat Pemakaman')
                ->maxLength(255)
                ->hint('Opsional')
                ->helperText('Lokasi pemakaman (cth: TPU, Makam Keluarga).')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            DatePicker::make('data_kematian.tanggal_pemakaman')
                ->label('Tanggal Pemakaman')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->hint('Opsional')
                ->helperText('Tanggal dilaksanakan pemakaman.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            Textarea::make('data_kematian.keterangan_tambahan')
                ->label('Keterangan Tambahan')
                ->maxLength(1000)
                ->rows(3)
                ->hint('Opsional')
                ->helperText('Catatan tambahan terkait kematian.')
                ->visible(fn (Get $get): bool => self::isVisible($get))
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
            return $jenisSurat === JenisSuratKeterangan::KEMATIAN;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::KEMATIAN->value;
    }
}
