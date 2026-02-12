<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Services\ApiWilayahService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Desa>
 */
class DesaFactory extends Factory
{
    private static ?array $availableVillages = null;

    private static int $currentIndex = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $apiWilayah = app(ApiWilayahService::class);

        // Ambil villages dari API hanya sekali
        if (self::$availableVillages === null) {
            $villages = $apiWilayah->getVillagesWonggedukuBarat();

            if ($villages->isEmpty()) {
                throw new \RuntimeException('Tidak dapat mengambil data desa dari API. Pastikan koneksi internet aktif.');
            }

            self::$availableVillages = $villages->toArray();
        }

        // Ambil village berdasarkan index
        if (self::$currentIndex >= count(self::$availableVillages)) {
            throw new \RuntimeException('Jumlah desa yang diminta melebihi jumlah desa yang tersedia di API.');
        }

        $village = self::$availableVillages[self::$currentIndex];
        self::$currentIndex++;

        return [
            'nama_desa' => $village['name'],
            'slug' => Str::slug($village['name']),
            'kode_desa' => $village['id'],
            'kode_provinsi' => '74',
            'nama_provinsi' => 'SULAWESI TENGGARA',
            'kode_kabupaten' => '7403',
            'nama_kabupaten' => 'KABUPATEN KONAWE',
            'kode_kecamatan' => '7403143',
            'nama_kecamatan' => 'WONGGEDUKU BARAT',
            'kecamatan' => 'WONGGEDUKU BARAT',
            'alamat' => fake()->address(),
            'telepon' => fake()->optional()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
        ];
    }
}
