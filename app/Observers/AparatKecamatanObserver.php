<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AparatKecamatan;
use Illuminate\Support\Facades\Storage;

class AparatKecamatanObserver
{
    /**
     * Handle the AparatKecamatan "updating" event.
     */
    public function updating(AparatKecamatan $aparatKecamatan): void
    {
        // Check if foto is being changed
        if ($aparatKecamatan->isDirty('foto')) {
            $oldFoto = $aparatKecamatan->getOriginal('foto');

            // Delete old foto if exists
            if ($oldFoto && Storage::disk('public')->exists($oldFoto)) {
                Storage::disk('public')->delete($oldFoto);
            }
        }

        // Check if foto_ttd is being changed
        if ($aparatKecamatan->isDirty('foto_ttd')) {
            $oldFotoTtd = $aparatKecamatan->getOriginal('foto_ttd');

            // Delete old foto_ttd if exists
            if ($oldFotoTtd && Storage::disk('public')->exists($oldFotoTtd)) {
                Storage::disk('public')->delete($oldFotoTtd);
            }
        }
    }

    /**
     * Handle the AparatKecamatan "deleted" event.
     */
    public function deleted(AparatKecamatan $aparatKecamatan): void
    {
        // Delete foto when aparat kecamatan is soft deleted
        if ($aparatKecamatan->foto && Storage::disk('public')->exists($aparatKecamatan->foto)) {
            Storage::disk('public')->delete($aparatKecamatan->foto);
        }

        // Delete foto_ttd when aparat kecamatan is soft deleted
        if ($aparatKecamatan->foto_ttd && Storage::disk('public')->exists($aparatKecamatan->foto_ttd)) {
            Storage::disk('public')->delete($aparatKecamatan->foto_ttd);
        }
    }

    /**
     * Handle the AparatKecamatan "restored" event.
     */
    public function restored(AparatKecamatan $aparatKecamatan): void
    {
        // Note: Files are already deleted on soft delete
        // User may need to re-upload files after restoration
    }

    /**
     * Handle the AparatKecamatan "force deleted" event.
     */
    public function forceDeleted(AparatKecamatan $aparatKecamatan): void
    {
        // Delete foto when aparat kecamatan is permanently deleted
        if ($aparatKecamatan->foto && Storage::disk('public')->exists($aparatKecamatan->foto)) {
            Storage::disk('public')->delete($aparatKecamatan->foto);
        }

        // Delete foto_ttd when aparat kecamatan is permanently deleted
        if ($aparatKecamatan->foto_ttd && Storage::disk('public')->exists($aparatKecamatan->foto_ttd)) {
            Storage::disk('public')->delete($aparatKecamatan->foto_ttd);
        }
    }
}
