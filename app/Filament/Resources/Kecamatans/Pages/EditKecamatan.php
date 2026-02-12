<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kecamatans\Pages;

use App\Filament\Resources\Kecamatans\KecamatanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditKecamatan extends EditRecord
{
    protected static string $resource = KecamatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->color('danger'),
            ForceDeleteAction::make()
                ->label('Hapus Permanen')
                ->icon('heroicon-o-trash')
                ->color('danger'),
            RestoreAction::make()
                ->label('Pulihkan')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('success'),
        ];
    }
}
