<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kelahirans\Pages;

use App\Filament\Resources\Kelahirans\KelahiranResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditKelahiran extends EditRecord
{
    protected static string $resource = KelahiranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Kelahiran')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            ForceDeleteAction::make()
                ->label('Hapus Selamanya')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            RestoreAction::make()
                ->label('Pulihkan Kelahiran')
                ->color('success')
                ->icon('heroicon-o-arrow-uturn-left'),
        ];
    }
}
