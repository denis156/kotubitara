<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Pages;

use App\Filament\Resources\SuratKeterangans\SuratKeteranganResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSuratKeterangan extends EditRecord
{
    protected static string $resource = SuratKeteranganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat Surat')
                ->icon('heroicon-o-eye')
                ->color('info'),
            DeleteAction::make()
                ->label('Hapus Surat')
                ->color('danger')
                ->icon('heroicon-o-trash'),
        ];
    }
}
