<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AparatDesa;
use Illuminate\Support\Facades\Storage;

class AparatDesaObserver
{
    /**
     * Handle the AparatDesa "updating" event.
     */
    public function updating(AparatDesa $aparatDesa): void
    {
        // Check if foto is being changed
        if ($aparatDesa->isDirty('foto')) {
            $oldFoto = $aparatDesa->getOriginal('foto');

            // Delete old foto if exists
            if ($oldFoto && Storage::disk('public')->exists($oldFoto)) {
                Storage::disk('public')->delete($oldFoto);
            }
        }

        // Check if foto_ttd is being changed
        if ($aparatDesa->isDirty('foto_ttd')) {
            $oldFotoTtd = $aparatDesa->getOriginal('foto_ttd');

            // Delete old foto_ttd if exists
            if ($oldFotoTtd && Storage::disk('public')->exists($oldFotoTtd)) {
                Storage::disk('public')->delete($oldFotoTtd);
            }
        }
    }

    /**
     * Handle the AparatDesa "deleted" event.
     */
    public function deleted(AparatDesa $aparatDesa): void
    {
        // Delete foto when aparat desa is soft deleted
        if ($aparatDesa->foto && Storage::disk('public')->exists($aparatDesa->foto)) {
            Storage::disk('public')->delete($aparatDesa->foto);
        }

        // Delete foto_ttd when aparat desa is soft deleted
        if ($aparatDesa->foto_ttd && Storage::disk('public')->exists($aparatDesa->foto_ttd)) {
            Storage::disk('public')->delete($aparatDesa->foto_ttd);
        }
    }

    /**
     * Handle the AparatDesa "restored" event.
     */
    public function restored(AparatDesa $aparatDesa): void
    {
        // Note: Files are already deleted on soft delete
        // User may need to re-upload files after restoration
    }

    /**
     * Handle the AparatDesa "force deleted" event.
     */
    public function forceDeleted(AparatDesa $aparatDesa): void
    {
        // Delete foto when aparat desa is permanently deleted
        if ($aparatDesa->foto && Storage::disk('public')->exists($aparatDesa->foto)) {
            Storage::disk('public')->delete($aparatDesa->foto);
        }

        // Delete foto_ttd when aparat desa is permanently deleted
        if ($aparatDesa->foto_ttd && Storage::disk('public')->exists($aparatDesa->foto_ttd)) {
            Storage::disk('public')->delete($aparatDesa->foto_ttd);
        }
    }
}
