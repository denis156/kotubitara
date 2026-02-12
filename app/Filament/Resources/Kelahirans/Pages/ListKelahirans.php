<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kelahirans\Pages;

use App\Filament\Resources\Kelahirans\KelahiranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKelahirans extends ListRecords
{
    protected static string $resource = KelahiranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Kelahiran')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
}
