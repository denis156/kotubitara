<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Desa;
use App\Services\ApiWilayahService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DesaObserver
{
    public function __construct(
        private ApiWilayahService $apiWilayah
    ) {}

    public function creating(Desa $desa): void
    {
        // Auto-fill kode_desa dari nama_desa dan kecamatan_id
        if (! empty($desa->nama_desa) && ! empty($desa->kecamatan_id)) {
            $this->fillKodeDesaFromNama($desa);
        }

        // Auto-generate slug
        if (empty($desa->slug) && ! empty($desa->nama_desa)) {
            $desa->slug = $this->generateUniqueSlug($desa->nama_desa);
        }
    }

    public function created(Desa $desa): void
    {
        // Attach desa to authenticated user otomatis
        if (Auth::check()) {
            $desa->users()->attach(Auth::id());
        }
    }

    public function updating(Desa $desa): void
    {
        // Auto-fill kode_desa jika nama_desa atau kecamatan_id berubah
        if (($desa->isDirty('nama_desa') || $desa->isDirty('kecamatan_id')) && ! empty($desa->nama_desa) && ! empty($desa->kecamatan_id)) {
            $this->fillKodeDesaFromNama($desa);
        }

        // Auto-generate slug jika nama_desa berubah
        if ($desa->isDirty('nama_desa') && ! empty($desa->nama_desa)) {
            $desa->slug = $this->generateUniqueSlug($desa->nama_desa);
        }
    }

    private function fillKodeDesaFromNama(Desa $desa): void
    {
        // Ambil kecamatan dari relasi
        $kecamatan = $desa->kecamatan;

        if ($kecamatan && ! empty($desa->nama_desa)) {
            // Cari kode desa dari nama desa berdasarkan kode kecamatan
            $villages = $this->apiWilayah->getVillages($kecamatan->kode_kecamatan);
            $village = $villages->firstWhere('name', $desa->nama_desa);

            if ($village) {
                $desa->kode_desa = $village['id'];
            }
        }
    }

    private function generateUniqueSlug(?string $namaDesa): string
    {
        if (empty($namaDesa)) {
            return '';
        }

        $slug = Str::slug($namaDesa);
        $originalSlug = $slug;
        $counter = 1;

        while (Desa::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
