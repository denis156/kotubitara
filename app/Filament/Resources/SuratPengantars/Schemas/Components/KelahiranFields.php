<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars\Schemas\Components;

use App\Enums\JenisSuratPengantar;
use App\Enums\JenisKelamin;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;

class KelahiranFields
{
    public static function make(): array
    {
        return [
            Select::make('data_kelahiran.kelahiran_id')
                ->label('Data Kelahiran')
                ->relationship('kelahiran', 'id')
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama_bayi} - {$record->tanggal_lahir->format('d/m/Y')}")
                ->searchable(['nama_bayi'])
                ->preload()
                ->hint('Opsional')
                ->helperText('Pilih dari data kelahiran yang sudah ada.')
                ->visible(fn (Get $get): bool => self::isVisible($get))
                ->columnSpanFull(),

            TextInput::make('data_kelahiran.nama_bayi')
                ->label('Nama Bayi')
                ->maxLength(255)
                ->hint('Opsional')
                ->helperText('Nama lengkap bayi yang lahir.')
                ->visible(fn (Get $get): bool => self::isVisible($get))
                ->columnSpanFull(),

            Select::make('data_kelahiran.jenis_kelamin')
                ->label('Jenis Kelamin Bayi')
                ->options(JenisKelamin::class)
                ->native(false)
                ->hint('Opsional')
                ->helperText('Pilih jenis kelamin bayi.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            DatePicker::make('data_kelahiran.tanggal_lahir')
                ->label('Tanggal Lahir')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                ->hint('Opsional')
                ->helperText('Tanggal kelahiran bayi.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TimePicker::make('data_kelahiran.waktu_lahir')
                ->label('Waktu Lahir')
                ->native(false)
                ->seconds(false)
                ->hint('Opsional')
                ->helperText('Waktu kelahiran (jam:menit).')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kelahiran.tempat_lahir')
                ->label('Tempat Lahir')
                ->maxLength(255)
                ->hint('Opsional')
                ->helperText('Lokasi kelahiran (cth: RS, Puskesmas, Rumah).')
                ->visible(fn (Get $get): bool => self::isVisible($get))
                ->columnSpanFull(),

            TextInput::make('data_kelahiran.nama_ayah')
                ->label('Nama Ayah')
                ->maxLength(255)
                ->hint('Opsional')
                ->helperText('Nama lengkap ayah bayi.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kelahiran.nama_ibu')
                ->label('Nama Ibu')
                ->maxLength(255)
                ->hint('Opsional')
                ->helperText('Nama lengkap ibu bayi.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kelahiran.berat_lahir')
                ->label('Berat Lahir (kg)')
                ->numeric()
                ->step(0.01)
                ->minValue(0)
                ->maxValue(10)
                ->hint('Opsional')
                ->helperText('Berat bayi dalam kilogram (cth: 3.2).')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kelahiran.panjang_lahir')
                ->label('Panjang Lahir (cm)')
                ->numeric()
                ->step(0.01)
                ->minValue(0)
                ->maxValue(100)
                ->hint('Opsional')
                ->helperText('Panjang bayi dalam sentimeter (cth: 50).')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            Textarea::make('data_kelahiran.keterangan_tambahan')
                ->label('Keterangan Tambahan')
                ->maxLength(1000)
                ->rows(3)
                ->hint('Opsional')
                ->helperText('Catatan tambahan terkait kelahiran.')
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

        if ($jenisSurat instanceof JenisSuratPengantar) {
            return $jenisSurat === JenisSuratPengantar::KELAHIRAN;
        }

        return (string) $jenisSurat === JenisSuratPengantar::KELAHIRAN->value;
    }
}
