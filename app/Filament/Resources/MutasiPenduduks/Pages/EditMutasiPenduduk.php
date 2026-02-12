<?php

declare(strict_types=1);

namespace App\Filament\Resources\MutasiPenduduks\Pages;

use App\Filament\Resources\MutasiPenduduks\MutasiPendudukResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditMutasiPenduduk extends EditRecord
{
    protected static string $resource = MutasiPendudukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Mutasi')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            ForceDeleteAction::make()
                ->label('Hapus Selamanya')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            RestoreAction::make()
                ->label('Pulihkan Mutasi')
                ->color('success')
                ->icon('heroicon-o-arrow-uturn-left'),
        ];
    }
}
