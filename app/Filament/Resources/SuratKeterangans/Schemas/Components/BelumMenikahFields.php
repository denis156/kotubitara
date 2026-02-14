<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class BelumMenikahFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            TextInput::make('data_pernikahan.tempat_lahir')
                ->label('Tempat Lahir')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Tempat lahir wajib diisi.',
                    'max' => 'Tempat lahir maksimal 255 karakter.',
                ]),

            DatePicker::make('data_pernikahan.tanggal_lahir')
                ->label('Tanggal Lahir')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                ->validationMessages([
                    'required' => 'Tanggal lahir wajib diisi.',
                    'before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
                ]),

            TextInput::make('data_pernikahan.pekerjaan')
                ->label('Pekerjaan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(255)
                ->columnSpanFull(),

            Textarea::make('data_pernikahan.alamat_lengkap')
                ->label('Alamat Lengkap')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->rows(3)
                ->maxLength(500)
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
            return $jenisSurat === JenisSuratKeterangan::BELUM_MENIKAH;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::BELUM_MENIKAH->value;
    }
}
