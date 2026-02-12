<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kecamatans\Pages;

use App\Filament\Resources\Kecamatans\KecamatanResource;
use App\Models\Kecamatan;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListKecamatans extends ListRecords
{
    protected static string $resource = KecamatanResource::class;

    public function mount(): void
    {
        parent::mount();

        // Jika user adalah petugas kecamatan, redirect ke edit kecamatan mereka
        $user = Auth::user();
        if ($user->isPetugasKecamatan()) {
            // Ambil kecamatan dari salah satu desa yang dikelola petugas kecamatan
            $firstDesa = $user->desas()->where('slug', '!=', 'semua-desa')->first();

            if ($firstDesa && $firstDesa->kecamatan) {
                $this->redirect(KecamatanResource::getUrl('edit', ['record' => $firstDesa->kecamatan]));
            } else {
                // Fallback ke kecamatan pertama jika tidak ada desa
                $firstKecamatan = Kecamatan::first();
                if ($firstKecamatan) {
                    $this->redirect(KecamatanResource::getUrl('edit', ['record' => $firstKecamatan]));
                }
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Kecamatan')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
}
