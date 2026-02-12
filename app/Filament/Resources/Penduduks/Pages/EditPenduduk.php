<?php

declare(strict_types=1);

namespace App\Filament\Resources\Penduduks\Pages;

use App\Filament\Resources\Penduduks\PendudukResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPenduduk extends EditRecord
{
    protected static string $resource = PendudukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Penduduk')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            ForceDeleteAction::make()
                ->label('Hapus Selamanya')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            RestoreAction::make()
                ->label('Pulihkan Penduduk')
                ->color('success')
                ->icon('heroicon-o-arrow-uturn-left'),
        ];
    }
}
