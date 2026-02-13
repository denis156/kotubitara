<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars\Schemas\Components;

use App\Enums\JenisSuratPengantar;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class PindahFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            Textarea::make('data_pindah.alamat_asal')
                ->label('Alamat Asal (Sekarang)')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),

            Textarea::make('data_pindah.alamat_tujuan')
                ->label('Alamat Tujuan (Baru)')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),

            Select::make('data_pindah.alasan_pindah')
                ->label('Alasan Pindah')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->options([
                    'pekerjaan' => 'Pekerjaan',
                    'pendidikan' => 'Pendidikan',
                    'keamanan' => 'Keamanan',
                    'kesehatan' => 'Kesehatan',
                    'perumahan' => 'Perumahan',
                    'keluarga' => 'Keluarga',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->searchable()
                ->columnSpanFull(),

            TextInput::make('data_pindah.jumlah_keluarga_pindah')
                ->label('Jumlah Keluarga yang Pindah')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->numeric()
                ->minValue(1)
                ->default(1)
                ->validationMessages([
                    'numeric' => 'Jumlah keluarga harus berupa angka.',
                    'min' => 'Jumlah keluarga minimal 1 orang.',
                ])
                ->helperText('Jumlah anggota keluarga yang ikut pindah'),

            DatePicker::make('data_pindah.rencana_tanggal_pindah')
                ->label('Rencana Tanggal Pindah')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->validationMessages([
                    'date' => 'Tanggal tidak valid.',
                ])
                ->helperText('Tanggal rencana pindah')
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
            return $jenisSurat === JenisSuratPengantar::PINDAH;
        }

        return (string) $jenisSurat === JenisSuratPengantar::PINDAH->value;
    }
}
