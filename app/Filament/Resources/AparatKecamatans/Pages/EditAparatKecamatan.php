<?php

namespace App\Filament\Resources\AparatKecamatans\Pages;

use App\Filament\Resources\AparatKecamatans\AparatKecamatanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAparatKecamatan extends EditRecord
{
    protected static string $resource = AparatKecamatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
