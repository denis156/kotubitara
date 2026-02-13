<?php

declare(strict_types=1);

namespace App\Observers;

use App\Helpers\SuratHelper;
use App\Models\SuratKeterangan;
use Illuminate\Support\Facades\Storage;

class SuratKeteranganObserver
{
    /**
     * Handle the SuratKeterangan "creating" event.
     * Generate nomor surat sebelum data disimpan.
     */
    public function creating(SuratKeterangan $suratKeterangan): void
    {
        // Auto generate nomor surat jika kosong
        if (empty($suratKeterangan->no_surat)) {
            $prefix = $suratKeterangan->jenis_surat->getPrefix();
            $suratKeterangan->no_surat = SuratHelper::generateNomorSurat(
                prefix: $prefix,
                modelClass: SuratKeterangan::class
            );
        }
    }

    /**
     * Handle the SuratKeterangan "updating" event.
     */
    public function updating(SuratKeterangan $suratKeterangan): void
    {
        // Auto generate nomor surat jika masih kosong (untuk data lama yang belum punya nomor)
        if (empty($suratKeterangan->no_surat)) {
            $prefix = $suratKeterangan->jenis_surat->getPrefix();
            $suratKeterangan->no_surat = SuratHelper::generateNomorSurat(
                prefix: $prefix,
                modelClass: SuratKeterangan::class
            );
        }

        // Handle file cleanup for foto_ttd_pemohon
        if ($suratKeterangan->isDirty('foto_ttd_pemohon')) {
            $oldFotoTtd = $suratKeterangan->getOriginal('foto_ttd_pemohon');
            if ($oldFotoTtd && Storage::disk('public')->exists($oldFotoTtd)) {
                Storage::disk('public')->delete($oldFotoTtd);
            }
        }

        // Handle file cleanup for data_pelapor JSON field
        if ($suratKeterangan->isDirty('data_pelapor')) {
            $oldDataPelapor = $suratKeterangan->getOriginal('data_pelapor');
            $newDataPelapor = $suratKeterangan->data_pelapor;

            // Delete old foto_ttd if changed
            if (isset($oldDataPelapor['foto_ttd']) &&
                ($oldDataPelapor['foto_ttd'] ?? null) !== ($newDataPelapor['foto_ttd'] ?? null)) {
                if (Storage::disk('public')->exists($oldDataPelapor['foto_ttd'])) {
                    Storage::disk('public')->delete($oldDataPelapor['foto_ttd']);
                }
            }
        }

        // Handle file cleanup for dokumen_pendukung JSON field
        if ($suratKeterangan->isDirty('dokumen_pendukung')) {
            $oldDokumen = $suratKeterangan->getOriginal('dokumen_pendukung');
            $newDokumen = $suratKeterangan->dokumen_pendukung;

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
     * Handle the SuratKeterangan "deleted" event.
     */
    public function deleted(SuratKeterangan $suratKeterangan): void
    {
        $this->deleteFiles($suratKeterangan);
    }

    /**
     * Handle the SuratKeterangan "restored" event.
     */
    public function restored(SuratKeterangan $suratKeterangan): void
    {
        // Note: Files are already deleted on soft delete
        // User may need to re-upload files after restoration
    }

    /**
     * Handle the SuratKeterangan "force deleted" event.
     */
    public function forceDeleted(SuratKeterangan $suratKeterangan): void
    {
        $this->deleteFiles($suratKeterangan);
    }

    /**
     * Delete all files associated with the surat keterangan.
     */
    private function deleteFiles(SuratKeterangan $suratKeterangan): void
    {
        // Delete foto_ttd_pemohon
        if ($suratKeterangan->foto_ttd_pemohon) {
            if (Storage::disk('public')->exists($suratKeterangan->foto_ttd_pemohon)) {
                Storage::disk('public')->delete($suratKeterangan->foto_ttd_pemohon);
            }
        }

        // Delete foto_ttd from data_pelapor
        if (isset($suratKeterangan->data_pelapor['foto_ttd'])) {
            $fotoTtd = $suratKeterangan->data_pelapor['foto_ttd'];
            if (Storage::disk('public')->exists($fotoTtd)) {
                Storage::disk('public')->delete($fotoTtd);
            }
        }

        // Delete all dokumen_pendukung
        if (is_array($suratKeterangan->dokumen_pendukung)) {
            foreach ($suratKeterangan->dokumen_pendukung as $file) {
                if (Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }
    }
}
