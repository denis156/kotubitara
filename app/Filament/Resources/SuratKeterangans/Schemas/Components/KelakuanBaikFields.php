<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas\Components;

use App\Enums\JenisSuratKeterangan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class KelakuanBaikFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            TextInput::make('data_tambahan.pekerjaan')
                ->label('Pekerjaan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Pekerjaan wajib diisi.',
                    'max' => 'Pekerjaan maksimal 255 karakter.',
                ])
                ->columnSpanFull(),

            Textarea::make('data_tambahan.alamat_lengkap')
                ->label('Alamat Lengkap')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->rows(3)
                ->maxLength(500)
                ->validationMessages([
                    'required' => 'Alamat lengkap wajib diisi.',
                    'max' => 'Alamat maksimal 500 karakter.',
                ])
                ->columnSpanFull(),

            Select::make('data_tambahan.keperluan_surat')
                ->label('Keperluan Surat')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->options([
                    'melamar_pekerjaan' => 'Melamar Pekerjaan',
                    'cpns_tni_polri' => 'CPNS/TNI/Polri',
                    'beasiswa' => 'Beasiswa',
                    'keperluan_sekolah' => 'Keperluan Sekolah/Kuliah',
                    'organisasi' => 'Keperluan Organisasi',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->searchable()
                ->helperText('Untuk apa surat ini diperlukan')
                ->columnSpanFull(),

            Textarea::make('data_tambahan.keterangan_kelakuan')
                ->label('Keterangan Kelakuan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->rows(4)
                ->maxLength(1000)
                ->default('Yang bersangkutan berkelakuan baik, tidak pernah terlibat tindak pidana, dan merupakan warga yang taat pada peraturan yang berlaku.')
                ->helperText('Deskripsi kelakuan yang bersangkutan')
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
            return $jenisSurat === JenisSuratKeterangan::KELAKUAN_BAIK;
        }

        return (string) $jenisSurat === JenisSuratKeterangan::KELAKUAN_BAIK->value;
    }
}
