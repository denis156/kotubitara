<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars\Schemas\Components;

use App\Enums\JenisSuratPengantar;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class SkckFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            Textarea::make('data_skck.alamat_lengkap')
                ->label('Alamat Lengkap')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->rows(3)
                ->maxLength(500)
                ->columnSpanFull(),

            TextInput::make('data_skck.pekerjaan')
                ->label('Pekerjaan')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->maxLength(255),

            Select::make('data_skck.keperluan')
                ->label('Keperluan SKCK')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->hint('Opsional')
                ->options([
                    'melamar_pekerjaan' => 'Melamar Pekerjaan',
                    'melamar_cpns_tni_polri' => 'Melamar CPNS/TNI/Polri',
                    'pencalonan_pejabat_publik' => 'Pencalonan Pejabat Publik',
                    'izin_senjata_api' => 'Izin Senjata Api',
                    'visa_luar_negeri' => 'Visa/Ke Luar Negeri',
                    'lainnya' => 'Lainnya',
                ])
                ->native(false)
                ->searchable()
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
            return $jenisSurat === JenisSuratPengantar::SKCK;
        }

        return (string) $jenisSurat === JenisSuratPengantar::SKCK->value;
    }
}
