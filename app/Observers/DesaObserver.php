<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Desa;
use Illuminate\Support\Str;
use App\Services\ApiWilayahService;
use Illuminate\Support\Facades\Auth;

class DesaObserver
{
    public function __construct(
        private ApiWilayahService $apiWilayah
    ) {
    }

    public function creating(Desa $desa): void
    {
        // Auto-fill semua kode wilayah dari nama wilayah
        if (! empty($desa->nama_desa)) {
            $this->fillKodeWilayahFromNama($desa);
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
        // Auto-fill semua kode wilayah jika nama wilayah berubah
        if ($desa->isDirty('nama_desa') || $desa->isDirty('nama_kecamatan') || $desa->isDirty('nama_kabupaten') || $desa->isDirty('nama_provinsi')) {
            $this->fillKodeWilayahFromNama($desa);
        }

        // Auto-generate slug jika nama_desa berubah
        if ($desa->isDirty('nama_desa') && ! empty($desa->nama_desa)) {
            $desa->slug = $this->generateUniqueSlug($desa->nama_desa);
        }
    }

    private function fillKodeWilayahFromNama(Desa $desa): void
    {
        // Cari kode provinsi dari nama provinsi
        if (! empty($desa->nama_provinsi)) {
            $provinces = $this->apiWilayah->getProvinces();
            $province = $provinces->firstWhere('name', $desa->nama_provinsi);

            if ($province) {
                $desa->kode_provinsi = $province['id'];

                // Cari kode kabupaten dari nama kabupaten
                if (! empty($desa->nama_kabupaten)) {
                    $regencies = $this->apiWilayah->getRegencies($province['id']);
                    $regency = $regencies->firstWhere('name', $desa->nama_kabupaten);

                    if ($regency) {
                        $desa->kode_kabupaten = $regency['id'];

                        // Cari kode kecamatan dari nama kecamatan
                        if (! empty($desa->nama_kecamatan)) {
                            $districts = $this->apiWilayah->getDistricts($regency['id']);
                            $district = $districts->firstWhere('name', $desa->nama_kecamatan);

                            if ($district) {
                                $desa->kode_kecamatan = $district['id'];
                                // Keep old 'kecamatan' field for backward compatibility
                                $desa->kecamatan = $district['name'];

                                // Cari kode desa dari nama desa
                                if (! empty($desa->nama_desa)) {
                                    $villages = $this->apiWilayah->getVillages($district['id']);
                                    $village = $villages->firstWhere('name', $desa->nama_desa);

                                    if ($village) {
                                        $desa->kode_desa = $village['id'];
                                    }
                                }
                            }
                        }
                    }
                }
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
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
