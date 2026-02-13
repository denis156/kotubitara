<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kematians\Pages;

use App\Filament\Resources\Kematians\KematianResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditKematian extends EditRecord
{
    protected static string $resource = KematianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Kematian')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            ForceDeleteAction::make()
                ->label('Hapus Selamanya')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            RestoreAction::make()
                ->label('Pulihkan Kematian')
                ->color('success')
                ->icon('heroicon-o-arrow-uturn-left'),
        ];
    }
}
