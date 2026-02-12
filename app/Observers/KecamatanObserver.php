<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Kecamatan;
use App\Services\ApiWilayahService;

class KecamatanObserver
{
    public function __construct(
        private ApiWilayahService $apiWilayah
    ) {}

    public function creating(Kecamatan $kecamatan): void
    {
        // Auto-fill semua kode wilayah dari nama wilayah
        if (! empty($kecamatan->nama_provinsi) && ! empty($kecamatan->nama_kabupaten) && ! empty($kecamatan->nama_kecamatan)) {
            $this->fillKodeWilayahFromNama($kecamatan);
        }
    }

    public function updating(Kecamatan $kecamatan): void
    {
        // Auto-fill semua kode wilayah jika nama wilayah berubah
        if ($kecamatan->isDirty('nama_provinsi') || $kecamatan->isDirty('nama_kabupaten') || $kecamatan->isDirty('nama_kecamatan')) {
            $this->fillKodeWilayahFromNama($kecamatan);
        }
    }

    private function fillKodeWilayahFromNama(Kecamatan $kecamatan): void
    {
        // Cari kode provinsi dari nama provinsi
        if (! empty($kecamatan->nama_provinsi)) {
            $provinces = $this->apiWilayah->getProvinces();
            $province = $provinces->firstWhere('name', $kecamatan->nama_provinsi);

            if ($province) {
                $kecamatan->kode_provinsi = $province['id'];

                // Cari kode kabupaten dari nama kabupaten
                if (! empty($kecamatan->nama_kabupaten)) {
                    $regencies = $this->apiWilayah->getRegencies($province['id']);
                    $regency = $regencies->firstWhere('name', $kecamatan->nama_kabupaten);

                    if ($regency) {
                        $kecamatan->kode_kabupaten = $regency['id'];

                        // Cari kode kecamatan dari nama kecamatan
                        if (! empty($kecamatan->nama_kecamatan)) {
                            $districts = $this->apiWilayah->getDistricts($regency['id']);
                            $district = $districts->firstWhere('name', $kecamatan->nama_kecamatan);

                            if ($district) {
                                $kecamatan->kode_kecamatan = $district['id'];
                            }
                        }
                    }
                }
            }
        }
    }
}
