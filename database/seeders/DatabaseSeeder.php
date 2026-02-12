<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AparatDesa;
use App\Models\Desa;
use App\Models\KartuKeluarga;
use App\Models\Kecamatan;
use App\Models\Kelahiran;
use App\Models\Kematian;
use App\Models\MutasiPenduduk;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding database...');

        // Create Kecamatan (only 1)
        $this->command->info('🏛️ Creating Kecamatan...');
        $kecamatan = Kecamatan::factory()->create();
        $this->command->info('✅ Created Kecamatan: '.$kecamatan->nama_kecamatan);

        // Create Desa
        $this->command->info('📍 Creating Desa...');
        $desas = Desa::factory(5)->create([
            'kecamatan_id' => $kecamatan->id,
        ]);
        $this->command->info('✅ Created '.$desas->count().' desas');

        // Create Super Admin (can access everything)
        $this->command->info('🦸 Creating Super Admin...');
        $superAdmin = User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@kotubitara.id',
        ]);
        $superAdmin->desas()->attach($desas->pluck('id'));
        $this->command->info('✅ Created Super Admin with access to all desas');

        // Create Petugas Kecamatan (can access all desas)
        $this->command->info('👤 Creating Petugas Kecamatan...');
        $petugasKecamatan = User::factory()->petugasKecamatan()->create([
            'name' => 'OPT Kecamatan',
            'email' => 'kecamatan@kotubitara.id',
        ]);
        $petugasKecamatan->desas()->attach($desas->pluck('id'));
        $this->command->info('✅ Created Petugas Kecamatan with access to all desas');

        // Create Petugas Desa for each desa
        $this->command->info('👥 Creating Petugas Desa...');
        foreach ($desas as $index => $desa) {
            $petugasDesa = User::factory()->petugasDesa()->create([
                'name' => 'Petugas '.$desa->nama_desa,
                'email' => 'desa'.($index + 1).'@kotubitara.id',
            ]);
            $petugasDesa->desas()->attach($desa->id);
        }
        $this->command->info('✅ Created '.$desas->count().' petugas desa');

        // Create Kartu Keluarga and Penduduk for each desa
        $this->command->info('🏠 Creating Kartu Keluarga and Penduduk...');
        $totalKK = 0;
        $totalPenduduk = 0;

        foreach ($desas as $desa) {
            // Create 3-5 KK per desa
            $jumlahKK = rand(3, 5);

            for ($i = 0; $i < $jumlahKK; $i++) {
                // Create Kartu Keluarga
                $kk = KartuKeluarga::factory()->create([
                    'desa_id' => $desa->id,
                ]);

                // Create Kepala Keluarga
                $kepalaKeluarga = Penduduk::factory()
                    ->kepalaKeluarga()
                    ->create([
                        'desa_id' => $desa->id,
                        'kartu_keluarga_id' => $kk->id,
                    ]);

                // Set kepala keluarga
                $kk->update(['kepala_keluarga_id' => $kepalaKeluarga->id]);

                // Create Istri/Suami (80% chance)
                if (rand(1, 10) <= 8) {
                    Penduduk::factory()
                        ->pasangan()
                        ->create([
                            'desa_id' => $desa->id,
                            'kartu_keluarga_id' => $kk->id,
                            'jenis_kelamin' => $kepalaKeluarga->jenis_kelamin->value === 'laki-laki'
                                ? 'perempuan'
                                : 'laki-laki',
                        ]);
                    $totalPenduduk++;
                }

                // Create Anak (0-3 children)
                $jumlahAnak = rand(0, 3);
                for ($j = 0; $j < $jumlahAnak; $j++) {
                    Penduduk::factory()
                        ->anak()
                        ->create([
                            'desa_id' => $desa->id,
                            'kartu_keluarga_id' => $kk->id,
                        ]);
                    $totalPenduduk++;
                }

                $totalKK++;
                $totalPenduduk++; // Count kepala keluarga
            }
        }

        $this->command->info('✅ Created '.$totalKK.' kartu keluarga');
        $this->command->info('✅ Created '.$totalPenduduk.' penduduk');

        // Create some penduduk without KK (10-15 per desa)
        $this->command->info('👤 Creating Penduduk without KK...');
        $pendudukTanpaKK = 0;
        foreach ($desas as $desa) {
            $jumlah = rand(10, 15);
            Penduduk::factory($jumlah)->create([
                'desa_id' => $desa->id,
                'kartu_keluarga_id' => null,
            ]);
            $pendudukTanpaKK += $jumlah;
        }
        $this->command->info('✅ Created '.$pendudukTanpaKK.' penduduk without KK');

        // Create Aparat Desa
        $this->command->info('👔 Creating Aparat Desa...');
        $totalAparat = 0;
        foreach ($desas as $desa) {
            // Create 5-8 aparat per desa
            $jumlah = rand(5, 8);
            AparatDesa::factory($jumlah)->create([
                'desa_id' => $desa->id,
            ]);
            $totalAparat += $jumlah;
        }
        $this->command->info('✅ Created '.$totalAparat.' aparat desa');

        // Create Data Demografi
        $this->command->info('📊 Creating Data Demografi...');

        // Create Kematian (1-2 per desa)
        $this->command->info('💀 Creating Kematian records...');
        $totalKematian = 0;
        foreach ($desas as $desa) {
            $jumlah = rand(1, 2);
            $pendudukDiDesa = Penduduk::where('desa_id', $desa->id)->inRandomOrder()->limit($jumlah)->get();

            foreach ($pendudukDiDesa as $penduduk) {
                Kematian::factory()->create([
                    'penduduk_id' => $penduduk->id,
                    'desa_id' => $desa->id,
                ]);
                $totalKematian++;
            }
        }
        $this->command->info('✅ Created '.$totalKematian.' kematian records');

        // Create Kelahiran (3-5 per desa)
        $this->command->info('👶 Creating Kelahiran records...');
        $totalKelahiran = 0;
        foreach ($desas as $desa) {
            $jumlah = rand(3, 5);

            for ($i = 0; $i < $jumlah; $i++) {
                // Try to get parents from same desa
                $ayah = Penduduk::where('desa_id', $desa->id)
                    ->where('jenis_kelamin', 'laki-laki')
                    ->inRandomOrder()
                    ->first();

                $ibu = Penduduk::where('desa_id', $desa->id)
                    ->where('jenis_kelamin', 'perempuan')
                    ->inRandomOrder()
                    ->first();

                Kelahiran::factory()->create([
                    'desa_id' => $desa->id,
                    'ayah_id' => $ayah?->id,
                    'ibu_id' => $ibu?->id,
                ]);
                $totalKelahiran++;
            }
        }
        $this->command->info('✅ Created '.$totalKelahiran.' kelahiran records');

        // Create Mutasi Penduduk (2-3 per desa)
        $this->command->info('🔄 Creating Mutasi Penduduk records...');
        $totalMutasi = 0;
        foreach ($desas as $desa) {
            $jumlah = rand(2, 3);
            $pendudukDiDesa = Penduduk::where('desa_id', $desa->id)->inRandomOrder()->limit($jumlah)->get();

            foreach ($pendudukDiDesa as $penduduk) {
                MutasiPenduduk::factory()->create([
                    'penduduk_id' => $penduduk->id,
                    'desa_id' => $desa->id,
                ]);
                $totalMutasi++;
            }
        }
        $this->command->info('✅ Created '.$totalMutasi.' mutasi penduduk records');

        // Fix nomor surat yang null (fallback jika Observer tidak jalan saat seeding)
        $this->command->info('🔧 Generating nomor surat...');
        $kematianNull = Kematian::whereNull('no_surat_kematian')->get();
        foreach ($kematianNull as $kematian) {
            $kematian->no_surat_kematian = Kematian::generateNoSurat();
            $kematian->saveQuietly(); // Save without triggering events
        }

        $kelahiranNull = Kelahiran::whereNull('no_surat_kelahiran')->get();
        foreach ($kelahiranNull as $kelahiran) {
            $kelahiran->no_surat_kelahiran = Kelahiran::generateNoSurat();
            $kelahiran->saveQuietly(); // Save without triggering events
        }
        $this->command->info('✅ Generated '.$kematianNull->count().' kematian & '.$kelahiranNull->count().' kelahiran nomor surat');

        $this->command->info('');
        $this->command->info('🎉 Database seeded successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('  - Kecamatan: '.Kecamatan::count());
        $this->command->info('  - Desa: '.$desas->count());
        $this->command->info('  - Users: '.User::count());
        $this->command->info('  - Kartu Keluarga: '.$totalKK);
        $this->command->info('  - Penduduk (in KK): '.$totalPenduduk);
        $this->command->info('  - Penduduk (no KK): '.$pendudukTanpaKK);
        $this->command->info('  - Total Penduduk: '.Penduduk::count());
        $this->command->info('  - Aparat Desa: '.$totalAparat);
        $this->command->info('  - Kematian: '.$totalKematian);
        $this->command->info('  - Kelahiran: '.$totalKelahiran);
        $this->command->info('  - Mutasi Penduduk: '.$totalMutasi);
        $this->command->info('');
        $this->command->info('🔑 Login credentials:');
        $this->command->info('  Super Admin:');
        $this->command->info('    Email: admin@kotubitara.id');
        $this->command->info('    Password: password');
        $this->command->info('');
        $this->command->info('  Petugas Kecamatan:');
        $this->command->info('    Email: kecamatan@kotubitara.id');
        $this->command->info('    Password: password');
        $this->command->info('');
        $this->command->info('  Petugas Desa:');
        $this->command->info('    Email: desa1@kotubitara.id (desa2, desa3, etc.)');
        $this->command->info('    Password: password');
    }
}
