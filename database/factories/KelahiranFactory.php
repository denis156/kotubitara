<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\HubunganPelapor;
use App\Enums\JenisKelamin;
use App\Models\Desa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kelahiran>
 */
class KelahiranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenisKelamin = fake()->randomElement(JenisKelamin::cases());
        $gender = $jenisKelamin === JenisKelamin::LAKI_LAKI ? 'male' : 'female';
        $tanggalLahir = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'desa_id' => Desa::factory(),
            'nama_bayi' => fake()->name($gender),
            'nik_bayi' => fake()->optional(0.5)->numerify('################'),
            'jenis_kelamin' => $jenisKelamin,
            'tanggal_lahir' => $tanggalLahir,
            'waktu_lahir' => fake()->optional(0.8)->time('H:i'),
            'tempat_lahir' => fake()->randomElement([
                'Rumah Sakit',
                'Puskesmas',
                'Rumah',
                'Klinik Bersalin',
            ]),
            'berat_lahir' => fake()->optional(0.8)->randomFloat(2, 2.5, 4.5),
            'panjang_lahir' => fake()->optional(0.8)->randomFloat(2, 45, 55),
            'ayah_id' => null, // Will be set if parent exists
            'ibu_id' => null, // Will be set if parent exists
            'keterangan' => fake()->optional(0.2)->sentence(),

            // Data Pelapor
            'nama_pelapor' => fake()->name(),
            'nik_pelapor' => fake()->optional(0.7)->numerify('################'),
            'hubungan_pelapor' => fake()->randomElement(HubunganPelapor::cases()),
            'alamat_pelapor' => fake()->optional(0.6)->address(),
            'telepon_pelapor' => fake()->optional(0.7)->numerify('08##########'),

            // Dokumen Pendukung (opsional)
            'foto_surat_rs' => null,
        ];
    }
}
