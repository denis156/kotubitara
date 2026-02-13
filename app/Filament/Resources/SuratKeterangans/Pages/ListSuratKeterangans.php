<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Pages;

use App\Filament\Resources\SuratKeterangans\SuratKeteranganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuratKeterangans extends ListRecords
{
    protected static string $resource = SuratKeteranganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
