<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class AhliWarisFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            TextInput::make('data_ahli_waris.nama_pewaris')
                ->label('Nama Pewaris (Yang Meninggal)')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Nama pewaris wajib diisi.',
                    'max' => 'Nama pewaris maksimal 255 karakter.',
                ])
                ->columnSpanFull(),

            TextInput::make('data_ahli_waris.nik_pewaris')
                ->label('NIK Pewaris')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(16)
                ->length(16)
                ->validationMessages([
                    'length' => 'NIK harus tepat 16 digit.',
                ]),

            DatePicker::make('data_ahli_waris.tanggal_meninggal')
                ->label('Tanggal Meninggal')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                ->validationMessages([
                    'required' => 'Tanggal meninggal wajib diisi.',
                    'before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
                ]),

            Select::make('data_ahli_waris.hubungan_dengan_pewaris')
                ->label('Hubungan dengan Pewaris')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->options([
                    'anak_kandung' => 'Anak Kandung',
                    'anak_tiri' => 'Anak Tiri',
                    'istri' => 'Istri',
                    'suami' => 'Suami',
                    'ayah' => 'Ayah',
                    'ibu' => 'Ibu',
                    'saudara_kandung' => 'Saudara Kandung',
                    'cucu' => 'Cucu',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->searchable()
                ->validationMessages([
                    'required' => 'Hubungan dengan pewaris wajib dipilih.',
                ])
                ->columnSpanFull(),

            Textarea::make('data_ahli_waris.keterangan_harta')
                ->label('Keterangan Harta Warisan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->rows(3)
                ->maxLength(500)
                ->helperText('Deskripsi harta yang diwariskan')
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
                ->helperText('Upload KK, akta kematian, dan sertifikat/bukti kepemilikan harta warisan. Maks 5 file @ 2MB')
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
            return $jenisSurat === JenisSuratKeterangan::AHLI_WARIS;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::AHLI_WARIS->value;
    }
}
