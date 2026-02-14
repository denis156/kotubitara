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

        // Handle file cleanup for data_tambahan JSON field (ttd, foto_ttd, dokumen)
        if ($suratKeterangan->isDirty('data_tambahan')) {
            $oldDataTambahan = $suratKeterangan->getOriginal('data_tambahan');
            $newDataTambahan = $suratKeterangan->data_tambahan;

            // Delete old foto_ttd_pemohon if changed
            if (isset($oldDataTambahan['foto_ttd_pemohon']) &&
                ($oldDataTambahan['foto_ttd_pemohon'] ?? null) !== ($newDataTambahan['foto_ttd_pemohon'] ?? null)) {
                if (Storage::disk('public')->exists($oldDataTambahan['foto_ttd_pemohon'])) {
                    Storage::disk('public')->delete($oldDataTambahan['foto_ttd_pemohon']);
                }
            }

            // Delete old dokumen_pendukung if changed
            if (isset($oldDataTambahan['dokumen_pendukung']) && is_array($oldDataTambahan['dokumen_pendukung'])) {
                $newDokumen = $newDataTambahan['dokumen_pendukung'] ?? [];
                foreach ($oldDataTambahan['dokumen_pendukung'] as $key => $oldFile) {
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
        // Delete foto_ttd_pemohon from data_tambahan
        if (isset($suratKeterangan->data_tambahan['foto_ttd_pemohon'])) {
            $fotoTtd = $suratKeterangan->data_tambahan['foto_ttd_pemohon'];
            if (Storage::disk('public')->exists($fotoTtd)) {
                Storage::disk('public')->delete($fotoTtd);
            }
        }

        // Delete all dokumen_pendukung from data_tambahan
        if (isset($suratKeterangan->data_tambahan['dokumen_pendukung']) &&
            is_array($suratKeterangan->data_tambahan['dokumen_pendukung'])) {
            foreach ($suratKeterangan->data_tambahan['dokumen_pendukung'] as $file) {
                if (Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }
    }
}
