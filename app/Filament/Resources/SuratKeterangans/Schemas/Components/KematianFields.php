<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use App\Models\Kematian;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class KematianFields
{
    public static function make(): array
    {
        return [
            Select::make('data_kematian.kematian_id')
                ->label('Data Kematian')
                ->options(function (Get $get) {
                    $desaId = $get('desa_id');
                    if (! $desaId) {
                        return [];
                    }

                    return Kematian::where('desa_id', $desaId)
                        ->with('penduduk')
                        ->get()
                        ->mapWithKeys(fn ($kematian) => [
                            $kematian->id => "{$kematian->penduduk->nama_lengkap} - {$kematian->tanggal_meninggal->format('d/m/Y')}",
                        ]);
                })
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, Set $set) {
                    if ($state) {
                        $kematian = Kematian::find($state);
                        if ($kematian) {
                            $set('data_kematian.tanggal_meninggal', $kematian->tanggal_meninggal?->format('Y-m-d'));
                            $set('data_kematian.waktu_meninggal', $kematian->waktu_meninggal);
                            $set('data_kematian.tempat_meninggal', $kematian->tempat_meninggal);
                            $set('data_kematian.sebab_kematian', $kematian->sebab_kematian);
                            $set('data_kematian.tempat_pemakaman', $kematian->tempat_pemakaman);
                            $set('data_kematian.tanggal_pemakaman', $kematian->tanggal_pemakaman?->format('Y-m-d'));
                            $set('data_kematian.keterangan_tambahan', $kematian->keterangan);

                            // Auto-fill pelapor dari data kematian jika ada
                            $set('data_kematian.nama_pelapor', $kematian->nama_pelapor);
                            $set('data_kematian.nik_pelapor', $kematian->nik_pelapor);
                            $set('data_kematian.hubungan_pelapor', $kematian->hubungan_pelapor);
                        }
                    }
                })
                ->helperText('Pilih dari data kematian yang sudah ada di desa ini. Field di bawah akan terisi otomatis.')
                ->visible(fn (Get $get): bool => self::isVisible($get))
                ->columnSpanFull(),

            TextInput::make('data_kematian.nama_pelapor')
                ->label('Nama Pelapor')
                ->maxLength(255)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kematian yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get))
                ->columnSpanFull(),

            TextInput::make('data_kematian.nik_pelapor')
                ->label('NIK Pelapor')
                ->maxLength(16)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kematian yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kematian.hubungan_pelapor')
                ->label('Hubungan Pelapor dengan Almarhum/Almarhumah')
                ->maxLength(255)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kematian yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            DatePicker::make('data_kematian.tanggal_meninggal')
                ->label('Tanggal Meninggal')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kematian yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TimePicker::make('data_kematian.waktu_meninggal')
                ->label('Waktu Meninggal')
                ->native(false)
                ->seconds(false)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kematian yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kematian.tempat_meninggal')
                ->label('Tempat Meninggal')
                ->maxLength(255)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kematian yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            TextInput::make('data_kematian.sebab_kematian')
                ->label('Sebab Kematian')
                ->maxLength(255)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kematian yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get))
                ->columnSpanFull(),

            TextInput::make('data_kematian.tempat_pemakaman')
                ->label('Tempat Pemakaman')
                ->maxLength(255)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kematian yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            DatePicker::make('data_kematian.tanggal_pemakaman')
                ->label('Tanggal Pemakaman')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kematian yang dipilih.')
                ->visible(fn (Get $get): bool => self::isVisible($get)),

            Textarea::make('data_kematian.keterangan_tambahan')
                ->label('Keterangan Tambahan')
                ->maxLength(1000)
                ->rows(3)
                ->readOnly()
                ->hint('Otomatis')
                ->helperText('Otomatis terisi dari data kematian yang dipilih.')
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
