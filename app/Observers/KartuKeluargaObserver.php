<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\KartuKeluarga;
use App\Models\Penduduk;

class KartuKeluargaObserver
{
    /**
     * Handle the KartuKeluarga "created" event.
     */
    public function created(KartuKeluarga $kartuKeluarga): void
    {
        // Set kartu_keluarga_id untuk kepala keluarga saat KK dibuat
        if ($kartuKeluarga->kepala_keluarga_id) {
            Penduduk::where('id', $kartuKeluarga->kepala_keluarga_id)
                ->whereNull('kartu_keluarga_id')
                ->update(['kartu_keluarga_id' => $kartuKeluarga->id]);
        }
    }

    /**
     * Handle the KartuKeluarga "updated" event.
     */
    public function updated(KartuKeluarga $kartuKeluarga): void
    {
        // Cek apakah kepala_keluarga_id berubah
        if ($kartuKeluarga->isDirty('kepala_keluarga_id')) {
            $oldKepalaKeluargaId = $kartuKeluarga->getOriginal('kepala_keluarga_id');
            $newKepalaKeluargaId = $kartuKeluarga->kepala_keluarga_id;

            // Clear kartu_keluarga_id dari kepala keluarga lama (jika ada)
            if ($oldKepalaKeluargaId) {
                Penduduk::where('id', $oldKepalaKeluargaId)
                    ->where('kartu_keluarga_id', $kartuKeluarga->id)
                    ->update(['kartu_keluarga_id' => null]);
            }

            // Set kartu_keluarga_id untuk kepala keluarga baru
            if ($newKepalaKeluargaId) {
                Penduduk::where('id', $newKepalaKeluargaId)
                    ->update(['kartu_keluarga_id' => $kartuKeluarga->id]);
            }
        }
    }

    /**
     * Handle the KartuKeluarga "deleted" event.
     */
    public function deleted(KartuKeluarga $kartuKeluarga): void
    {
        // Clear kartu_keluarga_id dari semua penduduk yang terdaftar di KK ini
        // Ini diperlukan karena soft delete tidak trigger database cascade
        Penduduk::where('kartu_keluarga_id', $kartuKeluarga->id)
            ->update(['kartu_keluarga_id' => null]);
    }

    /**
     * Handle the KartuKeluarga "restored" event.
     */
    public function restored(KartuKeluarga $kartuKeluarga): void
    {
        // Saat restore, kita hanya restore kepala keluarga
        // Anggota keluarga lainnya harus di-assign manual oleh user melalui edit form
        if ($kartuKeluarga->kepala_keluarga_id) {
            Penduduk::where('id', $kartuKeluarga->kepala_keluarga_id)
                ->update(['kartu_keluarga_id' => $kartuKeluarga->id]);
        }
    }

    /**
     * Handle the KartuKeluarga "force deleted" event.
     */
    public function forceDeleted(KartuKeluarga $kartuKeluarga): void
    {
        // Untuk force delete, database cascade (nullOnDelete) akan handle
        // secara otomatis karena ini adalah hard delete
        // Tidak perlu manual update
    }
}
