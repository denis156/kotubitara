<?php

declare(strict_types=1);

namespace App\Filament\Resources\KartuKeluargas\Pages;

use App\Filament\Resources\KartuKeluargas\KartuKeluargaResource;
use App\Models\Penduduk;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditKartuKeluarga extends EditRecord
{
    protected static string $resource = KartuKeluargaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Kartu Keluarga')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            ForceDeleteAction::make()
                ->label('Hapus Selamanya')
                ->color('danger')
                ->icon('heroicon-o-trash'),
            RestoreAction::make()
                ->label('Pulihkan Kartu Keluarga')
                ->color('success')
                ->icon('heroicon-o-arrow-uturn-left'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load anggota keluarga yang sudah terdaftar (exclude kepala keluarga)
        $data['anggota_keluarga'] = $this->record->anggotaKeluarga()
            ->when($this->record->kepala_keluarga_id, fn ($q) => $q->where('id', '!=', $this->record->kepala_keluarga_id))
            ->pluck('id')
            ->toArray();

        return $data;
    }

    protected array $anggotaKeluargaIds = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Simpan anggota keluarga IDs ke property class
        $this->anggotaKeluargaIds = $data['anggota_keluarga'] ?? [];
        unset($data['anggota_keluarga']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Observer sudah handle kepala keluarga
        // Kita handle anggota keluarga lainnya di sini

        // Set kartu_keluarga_id untuk anggota yang dipilih
        if (! empty($this->anggotaKeluargaIds)) {
            Penduduk::whereIn('id', $this->anggotaKeluargaIds)
                ->update(['kartu_keluarga_id' => $this->record->id]);
        }

        // Clear kartu_keluarga_id untuk yang tidak dipilih (exclude kepala keluarga)
        Penduduk::where('kartu_keluarga_id', $this->record->id)
            ->where('id', '!=', $this->record->kepala_keluarga_id)
            ->whereNotIn('id', $this->anggotaKeluargaIds)
            ->update(['kartu_keluarga_id' => null]);
    }
}
