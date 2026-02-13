<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\DatePicker;
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
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(10),

            TextInput::make('data_domisili.rw')
                ->label('RW')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(10),

            Textarea::make('data_domisili.alamat_lengkap')
                ->label('Alamat Lengkap')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->rows(3)
                ->maxLength(500)
                ->columnSpanFull(),

            DatePicker::make('data_domisili.sejak_tinggal')
                ->label('Tinggal Sejak')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                ->validationMessages([
                    'before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
                ])
                ->helperText('Sejak kapan tinggal di alamat ini')
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
