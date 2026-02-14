<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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

            Select::make('data_pernikahan.sebab')
                ->label('Sebab')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->options([
                    'cerai_mati' => 'Cerai Mati (Ditinggal Meninggal)',
                    'cerai_hidup' => 'Cerai Hidup (Bercerai)',
                ])
                ->native(false)
                ->validationMessages([
                    'required' => 'Sebab wajib dipilih.',
                ])
                ->helperText('Alasan menjadi janda/duda')
                ->columnSpanFull(),

            TextInput::make('data_pernikahan.nama_mantan_pasangan')
                ->label('Nama Mantan Pasangan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Nama mantan pasangan wajib diisi.',
                    'max' => 'Nama pasangan maksimal 255 karakter.',
                ])
                ->columnSpanFull(),

            DatePicker::make('data_pernikahan.tanggal_cerai_atau_meninggal')
                ->label('Tanggal Cerai/Meninggal')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                ->validationMessages([
                    'required' => 'Tanggal wajib diisi.',
                    'before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
                ])
                ->helperText('Tanggal pasangan meninggal atau tanggal perceraian')
                ->columnSpanFull(),

            FileUpload::make('data_tambahan.dokumen_pendukung')
                ->label('Dokumen Pendukung')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->multiple()
                ->image()
                ->imageEditor()
                ->disk('public')
                ->directory('surat-keterangan/dokumen')
                ->visibility('public')
                ->maxSize(2048)
                ->maxFiles(5)
                ->hint('Opsional')
                ->helperText('Upload akta kematian (cerai mati) atau surat cerai (cerai hidup). Maks 5 file @ 2MB')
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
