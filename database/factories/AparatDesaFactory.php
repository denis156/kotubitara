<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\JabatanAparat;
use App\Models\Desa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AparatDesa>
 */
class AparatDesaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jabatan = fake()->randomElement(JabatanAparat::cases());
        $namaDusun = $jabatan === JabatanAparat::KEPALA_DUSUN
            ? 'Dusun ' . fake()->randomElement(['I', 'II', 'III', 'IV', 'V'])
            : null;

        return [
            'desa_id' => Desa::factory(),
            'nama_lengkap' => fake()->name(),
            'nip' => fake()->boolean(70) ? fake()->numerify('##########') : null,
            'jabatan' => $jabatan,
            'nama_dusun' => $namaDusun,
            'telepon' => fake()->phoneNumber(),
            'email' => fake()->optional(0.7)->email(),
            'alamat' => fake()->optional(0.8)->address(),
            'tanggal_mulai_jabatan' => fake()->optional(0.9)->dateTimeBetween('-5 years', 'now'),
            'tanggal_selesai_jabatan' => fake()->optional(0.2)->dateTimeBetween('now', '+5 years'),
            'status' => fake()->boolean(90) ? 'aktif' : 'non-aktif',
        ];
    }
}
