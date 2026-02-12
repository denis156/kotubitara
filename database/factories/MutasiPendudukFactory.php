<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\JenisMutasi;
use App\Models\Desa;
use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MutasiPenduduk>
 */
class MutasiPendudukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenisMutasi = fake()->randomElement(JenisMutasi::cases());

        return [
            'penduduk_id' => Penduduk::factory(),
            'desa_id' => Desa::factory(),
            'jenis_mutasi' => $jenisMutasi,
            'tanggal_mutasi' => fake()->dateTimeBetween('-2 years', 'now'),
            'alamat_asal' => $jenisMutasi === JenisMutasi::PINDAH_MASUK
                ? fake()->address()
                : null,
            'alamat_tujuan' => $jenisMutasi === JenisMutasi::PINDAH_KELUAR
                ? fake()->address()
                : null,
            'alasan' => fake()->optional(0.7)->randomElement([
                'Pekerjaan',
                'Pendidikan',
                'Mengikuti Keluarga',
                'Pernikahan',
                'Lainnya',
            ]),
            'no_surat_pindah' => fake()->optional(0.6)->numerify('SP-####-####-####'),
            'keterangan' => fake()->optional(0.3)->sentence(),
        ];
    }
}
