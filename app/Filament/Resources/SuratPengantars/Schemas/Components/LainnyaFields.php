<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars\Schemas\Components;

use App\Enums\JenisSuratPengantar;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class LainnyaFields
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function make(): array
    {
        return [
            TextInput::make('data_tambahan.tujuan_surat')
                ->label('Tujuan Surat')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->maxLength(255)
                ->helperText('Untuk keperluan apa surat ini dibuat')
                ->columnSpanFull(),

            Textarea::make('data_tambahan.keterangan_lengkap')
                ->label('Keterangan Lengkap')
                ->visible(fn (Get $get) => static::isVisible($get))
                ->required(fn (Get $get) => static::isVisible($get))
                ->rows(5)
                ->maxLength(1000)
                ->helperText('Jelaskan secara lengkap keperluan surat pengantar ini')
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
            return $jenisSurat === JenisSuratPengantar::LAINNYA;
        }

        return (string) $jenisSurat === JenisSuratPengantar::LAINNYA->value;
    }
}
