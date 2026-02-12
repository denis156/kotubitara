<?php

declare(strict_types=1);

namespace App\Filament\Resources\Desas\Pages;

use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Desas\DesaResource;

class ListDesas extends ListRecords
{
    protected static string $resource = DesaResource::class;

    public function mount(): void
    {
        parent::mount();

        // Jika user adalah petugas desa, redirect ke edit desa pertama mereka
        $user = Auth::user();
        if ($user->isPetugasDesa()) {
            $firstDesa = $user->desas()->first();

            if ($firstDesa) {
                $this->redirect(DesaResource::getUrl('edit', ['record' => $firstDesa]));
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Desa')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
}
