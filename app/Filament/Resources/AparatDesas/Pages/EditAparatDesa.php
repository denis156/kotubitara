<?php

declare(strict_types=1);

namespace App\Filament\Resources\AparatDesas\Pages;

use App\Filament\Resources\AparatDesas\AparatDesaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAparatDesa extends EditRecord
{
    protected static string $resource = AparatDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Aparat Desa')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            ForceDeleteAction::make()
                ->label('Hapus Selamanya')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            RestoreAction::make()
                ->label('Pulihkan Aparat Desa')
                ->color('success')
                ->icon('heroicon-o-arrow-uturn-left'),
        ];
    }
}
