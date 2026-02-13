<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class JandaDudaFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            Select::make('data_pernikahan.status')
                ->label('Status')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->options([
                    'janda' => 'Janda',
                    'duda' => 'Duda',
                ])
                ->native(false)
                ->validationMessages([
                    'required' => 'Status wajib dipilih.',
                ])
                ->columnSpanFull(),

            TextInput::make('data_pernikahan.nama_pasangan_almarhum')
                ->label('Nama Pasangan (Almarhum/Almarhumah)')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Nama pasangan almarhum wajib diisi.',
                    'max' => 'Nama pasangan maksimal 255 karakter.',
                ])
                ->columnSpanFull(),

            DatePicker::make('data_pernikahan.tanggal_meninggal_pasangan')
                ->label('Tanggal Pasangan Meninggal')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                ->validationMessages([
                    'required' => 'Tanggal meninggal wajib diisi.',
                    'before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
                ])
                ->columnSpanFull(),

            DatePicker::make('data_pernikahan.tanggal_menikah')
                ->label('Tanggal Menikah (Dahulu)')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                ->helperText('Tanggal pernikahan dengan pasangan yang telah meninggal')
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
            return $jenisSurat === JenisSuratKeterangan::JANDA_DUDA;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::JANDA_DUDA->value;
    }
}
