<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Desa;
use Illuminate\Database\Seeder;

class SemuaDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Desa khusus "Semua Desa" untuk view all mode
        // Ini adalah special tenant untuk petugas kecamatan
        Desa::firstOrCreate(
            ['slug' => 'semua-desa'],
            [
                'nama_desa' => 'Semua Desa',
                'kode_desa' => '9999',
                'kode_provinsi' => '00',
                'nama_provinsi' => '-',
                'kode_kabupaten' => '00',
                'nama_kabupaten' => '-',
                'kode_kecamatan' => '00',
                'nama_kecamatan' => '-',
                'kecamatan' => '-',
                'alamat' => '-',
                'telepon' => '-',
                'email' => '-',
            ]
        );
    }
}
