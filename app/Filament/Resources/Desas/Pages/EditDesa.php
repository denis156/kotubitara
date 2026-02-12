<?php

declare(strict_types=1);

namespace App\Filament\Resources\Desas\Pages;

use App\Filament\Resources\Desas\DesaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditDesa extends EditRecord
{
    protected static string $resource = DesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Desa')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            ForceDeleteAction::make()
                ->label('Hapus Selamanya')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            RestoreAction::make()
                ->label('Pulihkan Desa')
                ->color('success')
                ->icon('heroicon-o-arrow-uturn-left'),
        ];
    }
}
