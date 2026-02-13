<?php

namespace App\Filament\Resources\AparatKecamatans\Pages;

use App\Filament\Resources\AparatKecamatans\AparatKecamatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAparatKecamatans extends ListRecords
{
    protected static string $resource = AparatKecamatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
