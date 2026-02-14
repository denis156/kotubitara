<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\JenisSuratKeterangan;
use App\Models\Desa;
use App\Models\Kematian;
use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuratKeterangan>
 */
class SuratKeteranganFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenisSurat = fake()->randomElement(JenisSuratKeterangan::cases());

        return [
            'desa_id' => Desa::factory(),
            'penduduk_id' => Penduduk::factory(),
            'kematian_id' => null, // Will be set for KEMATIAN type
            'jenis_surat' => $jenisSurat,
            'keperluan' => fake()->optional(0.7)->sentence(),

            // Data Spesifik (JSON) - semua data fleksibel ada di sini
            'data_domisili' => null,
            'data_usaha' => null,
            'data_ekonomi' => null,
            'data_pernikahan' => null,
            'data_ahli_waris' => null,
            'data_kematian' => null,
            'data_tambahan' => null, // untuk TTD, pelapor, dokumen, dll

            // Surat (no_surat temporary, will be updated by Observer if needed)
            'no_surat' => 'TEMP-'.fake()->numerify('####'),
            'tanggal_surat' => fake()->dateTimeBetween('-6 months', 'now'),
            'kepala_desa_id' => null, // Will be set in seeder
            'keterangan' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * State untuk jenis DOMISILI
     */
    public function domisili(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_surat' => JenisSuratKeterangan::DOMISILI,
            'data_domisili' => [
                // rt, rw, alamat_lengkap tidak perlu jika ada penduduk_id (diambil dari KK)
                'lama_tinggal' => fake()->numberBetween(1, 20).' tahun',
                'status_tempat_tinggal' => fake()->randomElement(['milik_sendiri', 'milik_orangtua', 'milik_keluarga', 'menumpang', 'kontrak', 'rumah_dinas']),
            ],
        ]);
    }

    /**
     * State untuk jenis KEMATIAN
     */
    public function kematian(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_surat' => JenisSuratKeterangan::KEMATIAN,
            'kematian_id' => Kematian::factory(),
            'data_kematian' => [
                'tanggal_meninggal' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                'tempat_meninggal' => fake()->randomElement(['Rumah', 'Rumah Sakit', 'Puskesmas']),
                'sebab_kematian' => fake()->randomElement(['Sakit', 'Usia Lanjut', 'Kecelakaan']),
            ],
        ]);
    }

    /**
     * State untuk jenis USAHA
     */
    public function usaha(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_surat' => JenisSuratKeterangan::USAHA,
            'data_usaha' => [
                'nama_usaha' => fake()->company(),
                'jenis_usaha' => fake()->randomElement(['warung_kelontong', 'toko_sembako', 'bengkel', 'penjahit', 'lainnya']),
                'alamat_usaha' => fake()->address(),
                'lama_usaha' => fake()->numberBetween(1, 10).' tahun',
            ],
        ]);
    }

    /**
     * State untuk jenis TIDAK_MAMPU
     */
    public function tidakMampu(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_surat' => JenisSuratKeterangan::TIDAK_MAMPU,
            'data_ekonomi' => [
                // pekerjaan tidak perlu jika ada penduduk_id (diambil dari penduduk)
                'penghasilan_perbulan' => fake()->numberBetween(500000, 2000000),
                'jumlah_tanggungan' => fake()->numberBetween(1, 5),
                'kondisi_rumah' => fake()->randomElement(['permanen', 'semi_permanen', 'kayu', 'bambu', 'darurat']),
                'kepemilikan_rumah' => fake()->randomElement(['milik_sendiri', 'milik_orangtua', 'menumpang', 'kontrak', 'rumah_dinas']),
                'alasan' => fake()->optional(0.7)->sentence(),
            ],
        ]);
    }

    /**
     * State untuk jenis PENGHASILAN
     */
    public function penghasilan(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_surat' => JenisSuratKeterangan::PENGHASILAN,
            'data_ekonomi' => [
                // pekerjaan tidak perlu jika ada penduduk_id (diambil dari penduduk)
                'jabatan' => fake()->optional(0.7)->jobTitle(),
                'tempat_bekerja' => fake()->optional(0.7)->company(),
                'penghasilan_perbulan' => fake()->numberBetween(3000000, 15000000),
                'sumber_penghasilan' => fake()->randomElement(['gaji_tetap', 'gaji_tidak_tetap', 'usaha_sendiri', 'wiraswasta', 'honorarium', 'lainnya']),
            ],
        ]);
    }

    /**
     * State untuk jenis BELUM_MENIKAH
     */
    public function belumMenikah(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_surat' => JenisSuratKeterangan::BELUM_MENIKAH,
        ]);
    }

    /**
     * State untuk jenis SUDAH_MENIKAH
     */
    public function sudahMenikah(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_surat' => JenisSuratKeterangan::SUDAH_MENIKAH,
            'data_pernikahan' => [
                'nama_pasangan' => fake()->name(),
                'tanggal_menikah' => fake()->dateTimeBetween('-10 years', '-1 year')->format('Y-m-d'),
                'tempat_menikah' => fake()->optional(0.7)->city(),
                'nomor_kutipan_akta_nikah' => fake()->optional(0.7)->numerify('####/AC/####/####'),
            ],
        ]);
    }

    /**
     * State untuk jenis AHLI_WARIS
     */
    public function ahliWaris(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_surat' => JenisSuratKeterangan::AHLI_WARIS,
            'data_ahli_waris' => [
                'nama_pewaris' => fake()->name(),
                'nik_pewaris' => fake()->optional(0.7)->numerify('################'),
                'tanggal_meninggal' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
                'hubungan_dengan_pewaris' => fake()->randomElement(['anak_kandung', 'anak_tiri', 'istri', 'suami', 'ayah', 'ibu', 'saudara_kandung', 'cucu']),
                'keterangan_harta' => fake()->optional(0.5)->sentence(),
            ],
        ]);
    }

    /**
     * State untuk jenis KEHILANGAN
     */
    public function kehilangan(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_surat' => JenisSuratKeterangan::KEHILANGAN,
            'data_tambahan' => [
                'jenis_barang_hilang' => fake()->randomElement(['ktp', 'kk', 'sim', 'stnk', 'ijazah', 'sertifikat_tanah', 'bpkb', 'dompet', 'handphone', 'sepeda_motor']),
                'nama_barang' => fake()->optional(0.7)->words(3, true),
                'tanggal_kehilangan' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                'tempat_kehilangan' => fake()->address(),
                'kronologi' => fake()->optional(0.7)->paragraph(),
            ],
        ]);
    }

    /**
     * State untuk jenis JANDA_DUDA
     */
    public function jandaDuda(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_surat' => JenisSuratKeterangan::JANDA_DUDA,
            'data_pernikahan' => [
                'status' => fake()->randomElement(['janda', 'duda']),
                'sebab' => fake()->randomElement(['cerai_mati', 'cerai_hidup']),
                'nama_mantan_pasangan' => fake()->name(),
                'tanggal_cerai_atau_meninggal' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * State untuk jenis KELAKUAN_BAIK
     */
    public function kelakuanBaik(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_surat' => JenisSuratKeterangan::KELAKUAN_BAIK,
            'data_tambahan' => [
                // pekerjaan dan alamat_lengkap tidak perlu jika ada penduduk_id
                'keperluan_surat' => fake()->randomElement(['melamar_pekerjaan', 'cpns_tni_polri', 'beasiswa', 'keperluan_sekolah', 'organisasi']),
                'keterangan_kelakuan' => 'Yang bersangkutan berkelakuan baik, tidak pernah terlibat tindak pidana, dan merupakan warga yang taat pada peraturan yang berlaku.',
            ],
        ]);
    }
}
