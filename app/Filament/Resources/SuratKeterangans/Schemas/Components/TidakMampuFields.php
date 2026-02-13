<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class TidakMampuFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            TextInput::make('data_ekonomi.penghasilan_perbulan')
                ->label('Penghasilan Per Bulan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->numeric()
                ->prefix('Rp')
                ->validationMessages([
                    'required' => 'Penghasilan per bulan wajib diisi.',
                    'numeric' => 'Penghasilan harus berupa angka.',
                ])
                ->helperText('Total penghasilan keluarga per bulan'),

            TextInput::make('data_ekonomi.jumlah_tanggungan')
                ->label('Jumlah Tanggungan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->numeric()
                ->minValue(0)
                ->validationMessages([
                    'required' => 'Jumlah tanggungan wajib diisi.',
                    'numeric' => 'Jumlah tanggungan harus berupa angka.',
                    'min' => 'Jumlah tanggungan tidak boleh kurang dari 0.',
                ])
                ->helperText('Jumlah anggota keluarga yang menjadi tanggungan'),

            Textarea::make('data_ekonomi.kondisi_rumah')
                ->label('Kondisi Rumah')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->rows(3)
                ->maxLength(500)
                ->helperText('Deskripsi kondisi tempat tinggal')
                ->columnSpanFull(),

            Textarea::make('data_ekonomi.alasan')
                ->label('Alasan Permohonan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->rows(3)
                ->maxLength(500)
                ->helperText('Alasan memerlukan surat keterangan tidak mampu')
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
            return $jenisSurat === JenisSuratKeterangan::TIDAK_MAMPU;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::TIDAK_MAMPU->value;
    }
}
