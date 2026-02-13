<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\HubunganPelapor;
use App\Models\Desa;
use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kematian>
 */
class KematianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tanggalMeninggal = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'penduduk_id' => Penduduk::factory(),
            'desa_id' => Desa::factory(),
            'tanggal_meninggal' => $tanggalMeninggal,
            'waktu_meninggal' => fake()->optional(0.6)->time(),
            'tempat_meninggal' => fake()->optional(0.8)->randomElement([
                'Rumah',
                'Rumah Sakit',
                'Puskesmas',
                'Dalam Perjalanan',
            ]),
            'sebab_kematian' => fake()->optional(0.8)->randomElement([
                'Sakit',
                'Kecelakaan',
                'Usia Lanjut',
                'Komplikasi Penyakit',
            ]),
            'tempat_pemakaman' => fake()->optional(0.7)->randomElement([
                'TPU Desa',
                'Makam Keluarga',
                'TPU Kecamatan',
            ]),
            'tanggal_pemakaman' => fake()->optional(0.7)->dateTimeBetween($tanggalMeninggal, '+3 days'),

            // Data Pelapor
            'nama_pelapor' => fake()->name(),
            'nik_pelapor' => fake()->optional(0.8)->numerify('################'),
            'hubungan_pelapor' => fake()->randomElement(HubunganPelapor::cases()),
            'alamat_pelapor' => fake()->optional(0.6)->address(),
            'telepon_pelapor' => fake()->optional(0.7)->numerify('08##########'),

            // Dokumen Pendukung (opsional)
            'foto_surat_rs' => null,

            'keterangan' => fake()->optional(0.3)->sentence(),
        ];
    }
}
