<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars\Schemas\Components;

use App\Enums\JenisSuratPengantar;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class KreditBankFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            Select::make('data_tambahan.jenis_kredit')
                ->label('Jenis Kredit')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->options([
                    'kpr' => 'KPR (Kredit Pemilikan Rumah)',
                    'kendaraan' => 'Kredit Kendaraan',
                    'usaha' => 'Kredit Usaha/Modal',
                    'multiguna' => 'Kredit Multiguna',
                    'pendidikan' => 'Kredit Pendidikan',
                    'tanpa_agunan' => 'Kredit Tanpa Agunan (KTA)',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->searchable()
                ->columnSpanFull(),

            TextInput::make('data_tambahan.nama_bank')
                ->label('Nama Bank/Lembaga Keuangan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->helperText('Contoh: Bank BRI, Bank BNI, Bank Mandiri, dll')
                ->columnSpanFull(),

            TextInput::make('data_tambahan.pekerjaan')
                ->label('Pekerjaan/Sumber Penghasilan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->columnSpanFull(),

            TextInput::make('data_tambahan.penghasilan_perbulan')
                ->label('Penghasilan Per Bulan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->numeric()
                ->prefix('Rp')
                ->validationMessages([
                    'numeric' => 'Penghasilan harus berupa angka.',
                ])
                ->helperText('Penghasilan rata-rata per bulan'),

            TextInput::make('data_tambahan.jumlah_pinjaman')
                ->label('Jumlah Pinjaman yang Diajukan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->numeric()
                ->prefix('Rp')
                ->validationMessages([
                    'numeric' => 'Jumlah pinjaman harus berupa angka.',
                ])
                ->helperText('Jumlah kredit yang akan diajukan'),

            Textarea::make('data_tambahan.keterangan')
                ->label('Keterangan Tambahan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->rows(3)
                ->maxLength(500)
                ->helperText('Informasi tambahan tentang kredit yang diajukan')
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
            return $jenisSurat === JenisSuratPengantar::KREDIT_BANK;
        }

        return (string) $jenisSurat === JenisSuratPengantar::KREDIT_BANK->value;
    }
}
