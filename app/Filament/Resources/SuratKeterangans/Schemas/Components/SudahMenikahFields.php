<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class SudahMenikahFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            TextInput::make('data_pernikahan.nama_pasangan')
                ->label('Nama Pasangan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Nama pasangan wajib diisi.',
                    'max' => 'Nama pasangan maksimal 255 karakter.',
                ])
                ->columnSpanFull(),

            DatePicker::make('data_pernikahan.tanggal_menikah')
                ->label('Tanggal Menikah')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                ->validationMessages([
                    'required' => 'Tanggal menikah wajib diisi.',
                    'before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
                ])
                ->columnSpanFull(),

            TextInput::make('data_pernikahan.tempat_menikah')
                ->label('Tempat Menikah')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(255),

            TextInput::make('data_pernikahan.nomor_kutipan_akta_nikah')
                ->label('Nomor Kutipan Akta Nikah')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(255),
        ];
    }

    protected static function isVisible(Get $get): bool
    {
        $jenisSurat = $get('jenis_surat');

        if ($jenisSurat === null) {
            return false;
        }

        if ($jenisSurat instanceof JenisSuratKeterangan) {
            return $jenisSurat === JenisSuratKeterangan::SUDAH_MENIKAH;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::SUDAH_MENIKAH->value;
    }
}
