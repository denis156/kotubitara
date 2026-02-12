<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kecamatan>
 */
class KecamatanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_provinsi' => '74',
            'nama_provinsi' => 'SULAWESI TENGGARA',
            'kode_kabupaten' => '7403',
            'nama_kabupaten' => 'KABUPATEN KONAWE',
            'kode_kecamatan' => '7403143',
            'nama_kecamatan' => 'WONGGEDUKU BARAT',
            'alamat' => 'Jl. Poros Unaaha - Lasolo',
            'telepon' => '(0401) 123456',
            'email' => 'kec.wonggedukubarat@konawekab.go.id',
            'website' => null,
        ];
    }
}
