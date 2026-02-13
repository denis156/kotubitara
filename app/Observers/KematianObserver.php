<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Kematian;
use Illuminate\Support\Facades\Storage;

class KematianObserver
{
    /**
     * Handle the Kematian "updating" event.
     */
    public function updating(Kematian $kematian): void
    {
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
        $this->deleteFiles($kematian);
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
        $this->deleteFiles($kematian);
    }

    /**
     * Delete all files associated with the kematian.
     */
    private function deleteFiles(Kematian $kematian): void
    {
        // Delete foto_surat_rs
        if ($kematian->foto_surat_rs && Storage::disk('public')->exists($kematian->foto_surat_rs)) {
            Storage::disk('public')->delete($kematian->foto_surat_rs);
        }
    }
}
