<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Kelahiran;
use Illuminate\Support\Facades\Storage;

class KelahiranObserver
{
    /**
     * Handle the Kelahiran "updating" event.
     */
    public function updating(Kelahiran $kelahiran): void
    {
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
        $this->deleteFiles($kelahiran);
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
        $this->deleteFiles($kelahiran);
    }

    /**
     * Delete all files associated with the kelahiran.
     */
    private function deleteFiles(Kelahiran $kelahiran): void
    {
        // Delete foto_surat_rs
        if ($kelahiran->foto_surat_rs && Storage::disk('public')->exists($kelahiran->foto_surat_rs)) {
            Storage::disk('public')->delete($kelahiran->foto_surat_rs);
        }
    }
}
