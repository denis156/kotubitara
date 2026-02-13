<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars\Schemas\Components;

use App\Enums\JenisKelamin;
use App\Enums\JenisSuratPengantar;
use App\Models\Kelahiran;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class KelahiranFields
{
    public static function make(): array
    {
        return [
            Select::make('data_kelahiran.kelahiran_id')
                ->label('Data Kelahiran')
                ->options(function (Get $get) {
                    $desaId = $get('desa_id');
                    if (! $desaId) {
                        return [];
                    }

                    return Kelahiran::where('desa_id', $desaId)
                        ->get()
                        ->mapWithKeys(fn ($kelahiran) => [
                            $kelahiran->id => "{$kelahiran->nama_bayi} - {$kelahiran->tanggal_lahir->format('d/m/Y')}",
                        ]);
                })
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, Set $set) {
                    if ($state) {
                        $kelahiran = Kelahiran::with(['ayah', 'ibu'])->find($state);
                        if ($kelahiran) {
                            $set('data_kelahiran.nama_bayi', $kelahiran->nama_bayi);
                            $set('data_kelahiran.jenis_kelamin', $kelahiran->jenis_kelamin?->value);
                            $set('data_kelahiran.tanggal_lahir', $kelahiran->tanggal_lahir?->format('Y-m-d'));
                            $set('data_kelahiran.waktu_lahir', $kelahiran->waktu_lahir);
                            $set('data_kelahiran.tempat_lahir', $kelahiran->tempat_lahir);
                            $set('data_kelahiran.nama_ayah', $kelahiran->ayah?->nama_lengkap);
                            $set('data_kelahiran.nama_ibu', $kelahiran->ibu?->nama_lengkap);
                            $set('data_kelahiran.berat_lahir', $kelahiran->berat_lahir);
                            $set('data_kelahiran.panjang_lahir', $kelahiran->panjang_lahir);
                            $set('data_kelahiran.keterangan_tambahan', $kelahiran->keterangan);
                        }
                    }
                })
                ->helperText('Pilih dari data kelahiran yang sudah ada di desa ini. Field di bawah akan terisi otomatis.')
                ->visible(fn (Get $get): bool => self::isVisible($get))
                ->columnSpanFull(),

            TextInput::make('data_kelahiran.nama_bayi')
                ->label('Nama Bayi')
                ->maxLength(255)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kelahiran yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get))
                ->columnSpanFull(),

            Select::make('data_kelahiran.jenis_kelamin')
                ->label('Jenis Kelamin Bayi')
                ->options(JenisKelamin::class)
                ->native(false)
                ->disabled()
                ->dehydrated()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kelahiran yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            DatePicker::make('data_kelahiran.tanggal_lahir')
                ->label('Tanggal Lahir')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kelahiran yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TimePicker::make('data_kelahiran.waktu_lahir')
                ->label('Waktu Lahir')
                ->native(false)
                ->seconds(false)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kelahiran yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kelahiran.tempat_lahir')
                ->label('Tempat Lahir')
                ->maxLength(255)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kelahiran yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get))
                ->columnSpanFull(),

            TextInput::make('data_kelahiran.nama_ayah')
                ->label('Nama Ayah')
                ->maxLength(255)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kelahiran yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kelahiran.nama_ibu')
                ->label('Nama Ibu')
                ->maxLength(255)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kelahiran yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kelahiran.berat_lahir')
                ->label('Berat Lahir (kg)')
                ->numeric()
                ->step(0.01)
                ->minValue(0)
                ->maxValue(10)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kelahiran yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kelahiran.panjang_lahir')
                ->label('Panjang Lahir (cm)')
                ->numeric()
                ->step(0.01)
                ->minValue(0)
                ->maxValue(100)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kelahiran yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            Textarea::make('data_kelahiran.keterangan_tambahan')
                ->label('Keterangan Tambahan')
                ->maxLength(1000)
                ->rows(3)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kelahiran yang dipilih.')
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
