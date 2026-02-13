<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars\Schemas\Components;

use App\Enums\JenisSuratPengantar;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class NikahFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            TextInput::make('data_nikah.nama_calon_pasangan')
                ->label('Nama Calon Pasangan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->columnSpanFull(),

            Textarea::make('data_nikah.alamat_calon_pasangan')
                ->label('Alamat Calon Pasangan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),

            TextInput::make('data_nikah.nama_wali')
                ->label('Nama Wali Nikah')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(255),

            TextInput::make('data_nikah.hubungan_wali')
                ->label('Hubungan dengan Wali')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(100)
                ->helperText('Contoh: Ayah Kandung, Kakek, dll'),

            DatePicker::make('data_nikah.rencana_tanggal_nikah')
                ->label('Rencana Tanggal Nikah')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->validationMessages([
                    'date' => 'Tanggal tidak valid.',
                ])
                ->helperText('Tanggal rencana pelaksanaan pernikahan')
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
            return $jenisSurat === JenisSuratPengantar::NIKAH;
        }

        return (string) $jenisSurat === JenisSuratPengantar::NIKAH->value;
    }
}
