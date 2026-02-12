<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ApiWilayahService
{
    private const BASE_URL = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    private const CACHE_TTL = 60 * 60 * 24 * 7; // 7 hari

    private const PROVINCE_SULAWESI_TENGGARA = '74';

    private const REGENCY_KONAWE = '7403';

    private const DISTRICT_WONGGEDUKU_BARAT = '7403143';

    public function getProvinces(): Collection
    {
        return Cache::remember('api_wilayah_provinces', self::CACHE_TTL, function () {
            $response = Http::get(self::BASE_URL . '/provinces.json');

            if ($response->successful()) {
                return collect($response->json());
            }

            return collect();
        });
    }

    public function getRegencies(string $provinceId): Collection
    {
        return Cache::remember("api_wilayah_regencies_{$provinceId}", self::CACHE_TTL, function () use ($provinceId) {
            $response = Http::get(self::BASE_URL . "/regencies/{$provinceId}.json");

            if ($response->successful()) {
                return collect($response->json());
            }

            return collect();
        });
    }

    public function getDistricts(string $regencyId): Collection
    {
        return Cache::remember("api_wilayah_districts_{$regencyId}", self::CACHE_TTL, function () use ($regencyId) {
            $response = Http::get(self::BASE_URL . "/districts/{$regencyId}.json");

            if ($response->successful()) {
                return collect($response->json());
            }

            return collect();
        });
    }

    public function getVillages(string $districtId): Collection
    {
        return Cache::remember("api_wilayah_villages_{$districtId}", self::CACHE_TTL, function () use ($districtId) {
            $response = Http::get(self::BASE_URL . "/villages/{$districtId}.json");

            if ($response->successful()) {
                return collect($response->json());
            }

            return collect();
        });
    }

    public function getVillagesWonggedukuBarat(): Collection
    {
        return $this->getVillages(self::DISTRICT_WONGGEDUKU_BARAT);
    }

    public function getVillageById(string $districtId, string $villageId): ?array
    {
        $villages = $this->getVillages($districtId);

        return $villages->firstWhere('id', $villageId);
    }

    public function getVillagesForSelect(string $districtId): array
    {
        return $this->getVillages($districtId)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getAvailableVillagesForSelect(string $districtId): array
    {
        $allVillages = $this->getVillages($districtId);

        // Get kode_desa yang sudah terdaftar
        $registeredKodeDesa = \App\Models\Desa::pluck('kode_desa')->toArray();

        // Filter villages yang belum terdaftar
        return $allVillages
            ->reject(fn ($village) => in_array($village['id'], $registeredKodeDesa))
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getVillagesWonggedukuBaratForSelect(): array
    {
        return $this->getVillagesForSelect(self::DISTRICT_WONGGEDUKU_BARAT);
    }

    public function getDistrictsKonawe(): Collection
    {
        return $this->getDistricts(self::REGENCY_KONAWE);
    }

    public function getDistrictsKonaweForSelect(): array
    {
        return $this->getDistricts(self::REGENCY_KONAWE)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getDistrictById(string $regencyId, string $districtId): ?array
    {
        $districts = $this->getDistricts($regencyId);

        return $districts->firstWhere('id', $districtId);
    }
}
