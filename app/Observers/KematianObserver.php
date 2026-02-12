<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Kematian;
use Illuminate\Support\Facades\Storage;

class KematianObserver
{
    /**
     * Handle the Kematian "creating" event.
     * Generate nomor surat sebelum data disimpan.
     */
    public function creating(Kematian $kematian): void
    {
        // Auto generate nomor surat jika kosong
        if (empty($kematian->no_surat_kematian)) {
            $kematian->no_surat_kematian = Kematian::generateNoSurat();
        }
    }

    /**
     * Handle the Kematian "updating" event.
     */
    public function updating(Kematian $kematian): void
    {
        // Check if foto_ttd_pelapor is being changed
        if ($kematian->isDirty('foto_ttd_pelapor')) {
            $oldFotoTtd = $kematian->getOriginal('foto_ttd_pelapor');

            // Delete old foto_ttd_pelapor if exists
            if ($oldFotoTtd && Storage::disk('public')->exists($oldFotoTtd)) {
                Storage::disk('public')->delete($oldFotoTtd);
            }
        }

        // Check if foto_surat_rs is being changed
        if ($kematian->isDirty('foto_surat_rs')) {
            $oldFotoSurat = $kematian->getOriginal('foto_surat_rs');

            // Delete old foto_surat_rs if exists
            if ($oldFotoSurat && Storage::disk('public')->exists($oldFotoSurat)) {
                Storage::disk('public')->delete($oldFotoSurat);
            }
        }
    }

    /**
     * Handle the Kematian "deleted" event.
     */
    public function deleted(Kematian $kematian): void
    {
        // Delete foto_ttd_pelapor when kematian is soft deleted
        if ($kematian->foto_ttd_pelapor && Storage::disk('public')->exists($kematian->foto_ttd_pelapor)) {
            Storage::disk('public')->delete($kematian->foto_ttd_pelapor);
        }

        // Delete foto_surat_rs when kematian is soft deleted
        if ($kematian->foto_surat_rs && Storage::disk('public')->exists($kematian->foto_surat_rs)) {
            Storage::disk('public')->delete($kematian->foto_surat_rs);
        }
    }

    /**
     * Handle the Kematian "restored" event.
     */
    public function restored(Kematian $kematian): void
    {
        // Note: Files are already deleted on soft delete
        // User may need to re-upload files after restoration
    }

    /**
     * Handle the Kematian "force deleted" event.
     */
    public function forceDeleted(Kematian $kematian): void
    {
        // Delete foto_ttd_pelapor when kematian is permanently deleted
        if ($kematian->foto_ttd_pelapor && Storage::disk('public')->exists($kematian->foto_ttd_pelapor)) {
            Storage::disk('public')->delete($kematian->foto_ttd_pelapor);
        }

        // Delete foto_surat_rs when kematian is permanently deleted
        if ($kematian->foto_surat_rs && Storage::disk('public')->exists($kematian->foto_surat_rs)) {
            Storage::disk('public')->delete($kematian->foto_surat_rs);
        }
    }
}
