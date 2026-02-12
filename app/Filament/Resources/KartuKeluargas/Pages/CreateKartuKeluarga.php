<?php

declare(strict_types=1);

namespace App\Filament\Resources\KartuKeluargas\Pages;

use App\Filament\Resources\KartuKeluargas\KartuKeluargaResource;
use App\Models\Penduduk;
use Filament\Resources\Pages\CreateRecord;

class CreateKartuKeluarga extends CreateRecord
{
    protected static string $resource = KartuKeluargaResource::class;

    protected array $anggotaKeluargaIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Simpan anggota keluarga IDs ke property class
        $this->anggotaKeluargaIds = $data['anggota_keluarga'] ?? [];
        unset($data['anggota_keluarga']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Observer sudah handle kepala keluarga
        // Kita handle anggota keluarga lainnya di sini
        if (! empty($this->anggotaKeluargaIds)) {
            Penduduk::whereIn('id', $this->anggotaKeluargaIds)
                ->update(['kartu_keluarga_id' => $this->record->id]);
        }
    }
}
