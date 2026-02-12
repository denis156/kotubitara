<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Agama;
use App\Enums\HubunganKeluarga;
use App\Enums\JenisKelamin;
use App\Enums\Kewarganegaraan;
use App\Enums\StatusPerkawinan;
use App\Models\Desa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Penduduk>
 */
class PendudukFactory extends Factory
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

        return [
            'nik' => fake()->unique()->numerify('################'),
            'kartu_keluarga_id' => null, // Will be set when attached to KK
            'desa_id' => Desa::factory(),
            'nama_lengkap' => fake()->name($gender),
            'jenis_kelamin' => $jenisKelamin,
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-70 years', '-1 year'),
            'agama' => fake()->randomElement(Agama::cases()),
            'status_perkawinan' => fake()->randomElement(StatusPerkawinan::cases()),
            'hubungan_keluarga' => fake()->randomElement(HubunganKeluarga::cases()),
            'pekerjaan' => fake()->optional(0.8)->jobTitle(),
            'pendidikan' => fake()->optional(0.8)->randomElement(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3']),
            'nama_ayah' => fake()->optional(0.9)->name('male'),
            'nama_ibu' => fake()->optional(0.9)->name('female'),
            'kewarganegaraan' => Kewarganegaraan::WNI,
        ];
    }

    /**
     * State for Kepala Keluarga
     */
    public function kepalaKeluarga(): static
    {
        return $this->state(fn (array $attributes) => [
            'hubungan_keluarga' => HubunganKeluarga::KEPALA_KELUARGA,
            'status_perkawinan' => fake()->randomElement([
                StatusPerkawinan::KAWIN,
                StatusPerkawinan::CERAI_MATI,
                StatusPerkawinan::CERAI_HIDUP,
            ]),
        ]);
    }

    /**
     * State for Istri/Suami
     */
    public function pasangan(): static
    {
        return $this->state(fn (array $attributes) => [
            'hubungan_keluarga' => $attributes['jenis_kelamin'] === JenisKelamin::PEREMPUAN
                ? HubunganKeluarga::ISTRI
                : HubunganKeluarga::SUAMI,
            'status_perkawinan' => StatusPerkawinan::KAWIN,
        ]);
    }

    /**
     * State for Anak
     */
    public function anak(): static
    {
        return $this->state(fn (array $attributes) => [
            'hubungan_keluarga' => HubunganKeluarga::ANAK,
            'tanggal_lahir' => fake()->dateTimeBetween('-25 years', '-1 year'),
            'status_perkawinan' => fake()->randomElement([
                StatusPerkawinan::BELUM_KAWIN,
                StatusPerkawinan::KAWIN,
            ]),
        ]);
    }
}
