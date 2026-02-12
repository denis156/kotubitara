<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Desa;
use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KartuKeluarga>
 */
class KartuKeluargaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_kk' => fake()->unique()->numerify('################'),
            'kepala_keluarga_id' => null, // Will be set after creating penduduk
            'desa_id' => Desa::factory(),
            'alamat' => fake()->address(),
            'rt' => fake()->numerify('###'),
            'rw' => fake()->numerify('###'),
        ];
    }
}
