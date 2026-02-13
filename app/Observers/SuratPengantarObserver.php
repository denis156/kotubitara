<?php

declare(strict_types=1);

namespace App\Observers;

use App\Helpers\SuratHelper;
use App\Models\SuratPengantar;
use Illuminate\Support\Facades\Storage;

class SuratPengantarObserver
{
    /**
     * Handle the SuratPengantar "creating" event.
     * Generate nomor surat sebelum data disimpan.
     */
    public function creating(SuratPengantar $suratPengantar): void
    {
        // Auto generate nomor surat jika kosong
        if (empty($suratPengantar->no_surat)) {
            $prefix = $suratPengantar->jenis_surat->getPrefix();
            $suratPengantar->no_surat = SuratHelper::generateNoSurat(
                prefix: $prefix,
                modelClass: SuratPengantar::class,
                columnName: 'no_surat'
            );
        }
    }

    /**
     * Handle the SuratPengantar "updating" event.
     */
    public function updating(SuratPengantar $suratPengantar): void
    {
        // Handle file cleanup for data_pelapor JSON field
        if ($suratPengantar->isDirty('data_pelapor')) {
            $oldDataPelapor = $suratPengantar->getOriginal('data_pelapor');
            $newDataPelapor = $suratPengantar->data_pelapor;

            // Delete old foto_ttd if changed
            if (isset($oldDataPelapor['foto_ttd']) &&
                ($oldDataPelapor['foto_ttd'] ?? null) !== ($newDataPelapor['foto_ttd'] ?? null)) {
                if (Storage::disk('public')->exists($oldDataPelapor['foto_ttd'])) {
                    Storage::disk('public')->delete($oldDataPelapor['foto_ttd']);
                }
            }
        }

        // Handle file cleanup for dokumen_pendukung JSON field
        if ($suratPengantar->isDirty('dokumen_pendukung')) {
            $oldDokumen = $suratPengantar->getOriginal('dokumen_pendukung');
            $newDokumen = $suratPengantar->dokumen_pendukung;

            if (is_array($oldDokumen)) {
                foreach ($oldDokumen as $key => $oldFile) {
                    // Delete if file is removed or changed
                    if (! isset($newDokumen[$key]) || $newDokumen[$key] !== $oldFile) {
                        if (Storage::disk('public')->exists($oldFile)) {
                            Storage::disk('public')->delete($oldFile);
                        }
                    }
                }
            }
        }
    }

    /**
     * Handle the SuratPengantar "deleted" event.
     */
    public function deleted(SuratPengantar $suratPengantar): void
    {
        $this->deleteFiles($suratPengantar);
    }

    /**
     * Handle the SuratPengantar "restored" event.
     */
    public function restored(SuratPengantar $suratPengantar): void
    {
        // Note: Files are already deleted on soft delete
        // User may need to re-upload files after restoration
    }

    /**
     * Handle the SuratPengantar "force deleted" event.
     */
    public function forceDeleted(SuratPengantar $suratPengantar): void
    {
        $this->deleteFiles($suratPengantar);
    }

    /**
     * Delete all files associated with the surat pengantar.
     */
    private function deleteFiles(SuratPengantar $suratPengantar): void
    {
        // Delete foto_ttd from data_pelapor
        if (isset($suratPengantar->data_pelapor['foto_ttd'])) {
            $fotoTtd = $suratPengantar->data_pelapor['foto_ttd'];
            if (Storage::disk('public')->exists($fotoTtd)) {
                Storage::disk('public')->delete($fotoTtd);
            }
        }

        // Delete all dokumen_pendukung
        if (is_array($suratPengantar->dokumen_pendukung)) {
            foreach ($suratPengantar->dokumen_pendukung as $file) {
                if (Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }
    }
}
