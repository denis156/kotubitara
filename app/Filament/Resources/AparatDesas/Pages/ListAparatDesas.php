<?php

declare(strict_types=1);

namespace App\Filament\Resources\AparatDesas\Pages;

use App\Filament\Resources\AparatDesas\AparatDesaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAparatDesas extends ListRecords
{
    protected static string $resource = AparatDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Aparat Desa')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
}
