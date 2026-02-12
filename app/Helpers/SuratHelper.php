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
     * Contoh: SK/KMT/2026/02/00001, SP/LHR/2026/02/00001
     *
     * @param  string  $prefix  Prefix surat (contoh: 'SK/KMT', 'SP/LHR')
     * @param  class-string<Model>  $modelClass  Nama class model
     * @param  string  $columnName  Nama kolom nomor surat di database
     * @return string Nomor surat yang di-generate
     */
    public static function generateNoSurat(string $prefix, string $modelClass, string $columnName): string
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

    /**
     * Generate nomor surat kematian.
     * Format: SK/KMT/YYYY/MM/XXXXX
     *
     * @return string
     */
    public static function generateNoSuratKematian(): string
    {
        return self::generateNoSurat(
            prefix: 'SK/KMT',
            modelClass: \App\Models\Kematian::class,
            columnName: 'no_surat_kematian'
        );
    }

    /**
     * Generate nomor surat pengantar kelahiran.
     * Format: SP/LHR/YYYY/MM/XXXXX
     *
     * @return string
     */
    public static function generateNoSuratKelahiran(): string
    {
        return self::generateNoSurat(
            prefix: 'SP/LHR',
            modelClass: \App\Models\Kelahiran::class,
            columnName: 'no_surat_kelahiran'
        );
    }
}
