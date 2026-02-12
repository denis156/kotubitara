<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class SemuaDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil kecamatan pertama, atau buat jika belum ada
        $kecamatan = Kecamatan::first();

        if (! $kecamatan) {
            $this->command->error('Tidak ada kecamatan. Jalankan DatabaseSeeder terlebih dahulu.');

            return;
        }

        // Buat Desa khusus "Semua Desa" untuk view all mode
        // Ini adalah special tenant untuk petugas kecamatan dan super admin
        Desa::firstOrCreate(
            ['slug' => 'semua-desa'],
            [
                'kecamatan_id' => $kecamatan->id,
                'nama_desa' => 'Semua Desa',
                'kode_desa' => '9999',
                'alamat' => null,
                'telepon' => null,
                'email' => null,
            ]
        );

        $this->command->info('✅ Desa "Semua Desa" berhasil dibuat/diperbarui.');
    }
}
