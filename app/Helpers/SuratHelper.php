<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class SuratHelper
{
    /**
     * Generate nomor surat otomatis dengan format yang konsisten.
     *
     * Format: PREFIX/YYYY/MM/XXXXX
     * Contoh: SK/DOM/2026/02/00001, SP/SKCK/2026/02/00001
     *
     * @param  string  $prefix  Prefix surat dari enum (contoh: 'SK/DOM', 'SP/SKCK')
     * @param  class-string<Model>  $modelClass  Nama class model (SuratKeterangan::class, SuratPengantar::class)
     * @param  string  $columnName  Nama kolom nomor surat di database (default: 'no_surat')
     * @return string Nomor surat yang di-generate
     */
    public static function generateNomorSurat(string $prefix, string $modelClass, string $columnName = 'no_surat'): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $fullPrefix = "{$prefix}/{$year}/{$month}";

        // Cari nomor terakhir (termasuk yang sudah di-soft delete)
        $lastRecord = $modelClass::withTrashed()
            ->where($columnName, 'like', "{$fullPrefix}/%")
            ->orderByRaw("CAST(SUBSTRING_INDEX({$columnName}, '/', -1) AS UNSIGNED) DESC")
            ->first();

        if ($lastRecord) {
            // Ambil 5 digit terakhir dan tambah 1
            $lastNumber = (int) substr($lastRecord->{$columnName}, -5);
            $newNumber = $lastNumber + 1;
        } else {
            // Mulai dari 1 jika belum ada
            $newNumber = 1;
        }

        // Format dengan 5 digit (00001, 00002, dst)
        return $fullPrefix.'/'.str_pad((string) $newNumber, 5, '0', STR_PAD_LEFT);
    }
}
