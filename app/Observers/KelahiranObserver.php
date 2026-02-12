<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Kelahiran;
use Illuminate\Support\Facades\Storage;

class KelahiranObserver
{
    /**
     * Handle the Kelahiran "creating" event.
     * Generate nomor surat sebelum data disimpan.
     */
    public function creating(Kelahiran $kelahiran): void
    {
        // Auto generate nomor surat jika kosong
        if (empty($kelahiran->no_surat_kelahiran)) {
            $kelahiran->no_surat_kelahiran = Kelahiran::generateNoSurat();
        }
    }

    /**
     * Handle the Kelahiran "updating" event.
     */
    public function updating(Kelahiran $kelahiran): void
    {
        // Check if foto_ttd_pelapor is being changed
        if ($kelahiran->isDirty('foto_ttd_pelapor')) {
            $oldFotoTtd = $kelahiran->getOriginal('foto_ttd_pelapor');

            // Delete old foto_ttd_pelapor if exists
            if ($oldFotoTtd && Storage::disk('public')->exists($oldFotoTtd)) {
                Storage::disk('public')->delete($oldFotoTtd);
            }
        }

        // Check if foto_surat_rs is being changed
        if ($kelahiran->isDirty('foto_surat_rs')) {
            $oldFotoSurat = $kelahiran->getOriginal('foto_surat_rs');

            // Delete old foto_surat_rs if exists
            if ($oldFotoSurat && Storage::disk('public')->exists($oldFotoSurat)) {
                Storage::disk('public')->delete($oldFotoSurat);
            }
        }
    }

    /**
     * Handle the Kelahiran "deleted" event.
     */
    public function deleted(Kelahiran $kelahiran): void
    {
        // Delete foto_ttd_pelapor when kelahiran is soft deleted
        if ($kelahiran->foto_ttd_pelapor && Storage::disk('public')->exists($kelahiran->foto_ttd_pelapor)) {
            Storage::disk('public')->delete($kelahiran->foto_ttd_pelapor);
        }

        // Delete foto_surat_rs when kelahiran is soft deleted
        if ($kelahiran->foto_surat_rs && Storage::disk('public')->exists($kelahiran->foto_surat_rs)) {
            Storage::disk('public')->delete($kelahiran->foto_surat_rs);
        }
    }

    /**
     * Handle the Kelahiran "restored" event.
     */
    public function restored(Kelahiran $kelahiran): void
    {
        // Note: Files are already deleted on soft delete
        // User may need to re-upload files after restoration
    }

    /**
     * Handle the Kelahiran "force deleted" event.
     */
    public function forceDeleted(Kelahiran $kelahiran): void
    {
        // Delete foto_ttd_pelapor when kelahiran is permanently deleted
        if ($kelahiran->foto_ttd_pelapor && Storage::disk('public')->exists($kelahiran->foto_ttd_pelapor)) {
            Storage::disk('public')->delete($kelahiran->foto_ttd_pelapor);
        }

        // Delete foto_surat_rs when kelahiran is permanently deleted
        if ($kelahiran->foto_surat_rs && Storage::disk('public')->exists($kelahiran->foto_surat_rs)) {
            Storage::disk('public')->delete($kelahiran->foto_surat_rs);
        }
    }
}
