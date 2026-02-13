<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class KehilanganFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            Select::make('data_tambahan.jenis_barang_hilang')
                ->label('Jenis Barang yang Hilang')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->options([
                    'ktp' => 'KTP',
                    'kk' => 'Kartu Keluarga',
                    'sim' => 'SIM',
                    'stnk' => 'STNK',
                    'ijazah' => 'Ijazah',
                    'sertifikat_tanah' => 'Sertifikat Tanah',
                    'bpkb' => 'BPKB',
                    'dompet' => 'Dompet',
                    'handphone' => 'Handphone',
                    'sepeda_motor' => 'Sepeda Motor',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->searchable()
                ->validationMessages([
                    'required' => 'Jenis barang yang hilang wajib dipilih.',
                ])
                ->columnSpanFull(),

            TextInput::make('data_tambahan.nama_barang')
                ->label('Nama/Merk Barang')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(255)
                ->helperText('Untuk kendaraan: merk & tipe, untuk dokumen: nomor dokumen')
                ->columnSpanFull(),

            DatePicker::make('data_tambahan.tanggal_kehilangan')
                ->label('Tanggal Kehilangan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                ->validationMessages([
                    'required' => 'Tanggal kehilangan wajib diisi.',
                    'before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
                ])
                ->columnSpanFull(),

            Textarea::make('data_tambahan.tempat_kehilangan')
                ->label('Tempat Kehilangan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->rows(2)
                ->maxLength(500)
                ->validationMessages([
                    'required' => 'Tempat kehilangan wajib diisi.',
                    'max' => 'Tempat kehilangan maksimal 500 karakter.',
                ])
                ->helperText('Lokasi atau tempat terakhir kali barang terlihat')
                ->columnSpanFull(),

            Textarea::make('data_tambahan.kronologi')
                ->label('Kronologi Kejadian')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->rows(4)
                ->maxLength(1000)
                ->helperText('Ceritakan bagaimana kejadian kehilangan tersebut')
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
            return $jenisSurat === JenisSuratKeterangan::KEHILANGAN;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::KEHILANGAN->value;
    }
}
