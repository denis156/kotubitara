<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars\Schemas;

use App\Enums\JenisSuratPengantar;
use App\Helpers\DesaFieldHelper;
use App\Models\AparatDesa;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class SuratPengantarForm
{
    protected static function isJenisSurat(mixed $value, JenisSuratPengantar $jenis): bool
    {
        // Handle null
        if ($value === null) {
            return false;
        }

        // If it's already an enum instance
        if ($value instanceof JenisSuratPengantar) {
            return $value === $jenis;
        }

        // If it's a string value (most common in forms)
        return (string) $value === $jenis->value;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // Step 1: Pilih Jenis Surat, Data Pemohon & Tujuan
                    Step::make('Jenis Surat & Pemohon')
                        ->description('Pilih jenis surat dan data pemohon')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Select::make('jenis_surat')
                                ->label('Jenis Surat Pengantar')
                                ->options(JenisSuratPengantar::class)
                                ->required()
                                ->live()
                                ->native(false)
                                ->searchable()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if ($state instanceof JenisSuratPengantar) {
                                        $tujuanDefault = $state->getTujuanDefault();
                                        if ($tujuanDefault) {
                                            $set('ditujukan_kepada', $tujuanDefault);
                                        }
                                    }
                                })
                                ->helperText('Pilih jenis surat pengantar yang akan dibuat')
                                ->columnSpanFull(),

                            Select::make('desa_id')
                                ->label('Desa')
                                ->relationship('desa', 'nama_desa')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->default(fn () => DesaFieldHelper::getDefaultDesaId())
                                ->disabled(fn () => DesaFieldHelper::shouldDisableDesaField())
                                ->dehydrated()
                                ->helperText(fn () => DesaFieldHelper::getDesaFieldHint())
                                ->columnSpanFull(),

                            Select::make('penduduk_id')
                                ->label('Nama Pemohon')
                                ->relationship('penduduk', 'nama_lengkap')
                                ->searchable(['nama_lengkap', 'nik'])
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if ($state) {
                                        $penduduk = \App\Models\Penduduk::find($state);
                                        if ($penduduk) {
                                            $set('nama_pemohon', $penduduk->nama_lengkap);
                                            $set('nik_pemohon', $penduduk->nik);
                                        }
                                    }
                                })
                                ->helperText('Cari berdasarkan nama atau NIK')
                                ->columnSpanFull(),

                            TextInput::make('nama_pemohon')
                                ->label('Nama Pemohon')
                                ->required()
                                ->maxLength(255)
                                ->readOnly()
                                ->hint('Otomatis')
                                ->helperText('Otomatis terisi dari data penduduk'),

                            TextInput::make('nik_pemohon')
                                ->label('NIK Pemohon')
                                ->required()
                                ->maxLength(16)
                                ->readOnly()
                                ->hint('Otomatis')
                                ->helperText('Otomatis terisi dari data penduduk'),

                            Textarea::make('ditujukan_kepada')
                                ->label('Ditujukan Kepada')
                                ->required()
                                ->rows(2)
                                ->maxLength(500)
                                ->helperText('Otomatis terisi berdasarkan jenis surat, bisa diubah')
                                ->columnSpanFull(),

                            TextInput::make('keperluan')
                                ->label('Keperluan')
                                ->maxLength(255)
                                ->hint('Opsional')
                                ->helperText('Untuk apa surat ini dibuat')
                                ->columnSpanFull(),
                        ]),

                    // Step 2: Data Spesifik (Dynamic based on jenis_surat)
                    Step::make('Data Spesifik')
                        ->description('Isi data sesuai jenis surat')
                        ->icon('heroicon-o-pencil-square')
                        ->schema([
                            // Data SKCK - hanya muncul jika jenis = skck
                            Textarea::make('data_skck.alamat_lengkap')
                                ->label('Alamat Lengkap')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::SKCK))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::SKCK))
                                ->rows(3)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            TextInput::make('data_skck.pekerjaan')
                                ->label('Pekerjaan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::SKCK))
                                ->hint('Opsional')
                                ->maxLength(255),

                            Select::make('data_skck.keperluan')
                                ->label('Keperluan SKCK')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::SKCK))
                                ->hint('Opsional')
                                ->options([
                                    'melamar_pekerjaan' => 'Melamar Pekerjaan',
                                    'melamar_cpns_tni_polri' => 'Melamar CPNS/TNI/Polri',
                                    'pencalonan_pejabat_publik' => 'Pencalonan Pejabat Publik',
                                    'izin_senjata_api' => 'Izin Senjata Api',
                                    'visa_luar_negeri' => 'Visa/Ke Luar Negeri',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->native(false)
                                ->searchable()
                                ->columnSpanFull(),

                            // Data Nikah - hanya muncul jika jenis = nikah
                            TextInput::make('data_nikah.nama_calon_pasangan')
                                ->label('Nama Calon Pasangan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::NIKAH))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::NIKAH))
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Textarea::make('data_nikah.alamat_calon_pasangan')
                                ->label('Alamat Calon Pasangan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::NIKAH))
                                ->hint('Opsional')
                                ->rows(2)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            TextInput::make('data_nikah.nama_wali')
                                ->label('Nama Wali Nikah')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::NIKAH))
                                ->hint('Opsional')
                                ->maxLength(255),

                            TextInput::make('data_nikah.hubungan_wali')
                                ->label('Hubungan dengan Wali')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::NIKAH))
                                ->hint('Opsional')
                                ->maxLength(100)
                                ->helperText('Contoh: Ayah Kandung, Kakek, dll'),

                            DatePicker::make('data_nikah.rencana_tanggal_nikah')
                                ->label('Rencana Tanggal Nikah')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::NIKAH))
                                ->hint('Opsional')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->validationMessages([
                                    'date' => 'Tanggal tidak valid.',
                                ])
                                ->helperText('Tanggal rencana pelaksanaan pernikahan')
                                ->columnSpanFull(),

                            // Data Pindah - hanya muncul jika jenis = pindah
                            Textarea::make('data_pindah.alamat_asal')
                                ->label('Alamat Asal (Sekarang)')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::PINDAH))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::PINDAH))
                                ->rows(2)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            Textarea::make('data_pindah.alamat_tujuan')
                                ->label('Alamat Tujuan (Baru)')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::PINDAH))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::PINDAH))
                                ->rows(2)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            Select::make('data_pindah.alasan_pindah')
                                ->label('Alasan Pindah')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::PINDAH))
                                ->hint('Opsional')
                                ->options([
                                    'pekerjaan' => 'Pekerjaan',
                                    'pendidikan' => 'Pendidikan',
                                    'keamanan' => 'Keamanan',
                                    'kesehatan' => 'Kesehatan',
                                    'perumahan' => 'Perumahan',
                                    'keluarga' => 'Keluarga',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->native(false)
                                ->searchable()
                                ->columnSpanFull(),

                            TextInput::make('data_pindah.jumlah_keluarga_pindah')
                                ->label('Jumlah Keluarga yang Pindah')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::PINDAH))
                                ->hint('Opsional')
                                ->numeric()
                                ->minValue(1)
                                ->default(1)
                                ->validationMessages([
                                    'numeric' => 'Jumlah keluarga harus berupa angka.',
                                    'min' => 'Jumlah keluarga minimal 1 orang.',
                                ])
                                ->helperText('Jumlah anggota keluarga yang ikut pindah'),

                            DatePicker::make('data_pindah.rencana_tanggal_pindah')
                                ->label('Rencana Tanggal Pindah')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::PINDAH))
                                ->hint('Opsional')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->validationMessages([
                                    'date' => 'Tanggal tidak valid.',
                                ])
                                ->helperText('Tanggal rencana pindah')
                                ->columnSpanFull(),

                            // Data Dokumen (KTP/KK) - hanya muncul jika jenis = ktp_kk
                            Select::make('data_dokumen.jenis_dokumen')
                                ->label('Jenis Dokumen')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::KTP_KK))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::KTP_KK))
                                ->options([
                                    'ktp_baru' => 'Pembuatan KTP Baru',
                                    'ktp_hilang' => 'KTP Hilang',
                                    'ktp_rusak' => 'KTP Rusak',
                                    'kk_baru' => 'Pembuatan KK Baru',
                                    'kk_hilang' => 'KK Hilang',
                                    'kk_rusak' => 'KK Rusak',
                                    'kk_tambah_anggota' => 'KK Tambah Anggota Keluarga',
                                    'kk_kurang_anggota' => 'KK Kurang Anggota Keluarga',
                                ])
                                ->native(false)
                                ->searchable()
                                ->columnSpanFull(),

                            Textarea::make('data_dokumen.keterangan')
                                ->label('Keterangan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::KTP_KK))
                                ->hint('Opsional')
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('Keterangan tambahan tentang dokumen')
                                ->columnSpanFull(),

                            // Data Berobat - hanya muncul jika jenis = berobat
                            TextInput::make('data_tambahan.nama_pasien')
                                ->label('Nama Pasien')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::BEROBAT))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::BEROBAT))
                                ->maxLength(255)
                                ->validationMessages([
                                    'required' => 'Nama pasien wajib diisi.',
                                    'max' => 'Nama pasien maksimal 255 karakter.',
                                ])
                                ->helperText('Jika berbeda dengan pemohon')
                                ->columnSpanFull(),

                            Select::make('data_tambahan.hubungan_dengan_pasien')
                                ->label('Hubungan dengan Pasien')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::BEROBAT))
                                ->hint('Opsional')
                                ->options([
                                    'diri_sendiri' => 'Diri Sendiri',
                                    'anak' => 'Anak',
                                    'orang_tua' => 'Orang Tua',
                                    'suami' => 'Suami',
                                    'istri' => 'Istri',
                                    'saudara' => 'Saudara',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->native(false)
                                ->default('diri_sendiri')
                                ->columnSpanFull(),

                            Textarea::make('data_tambahan.keluhan')
                                ->label('Keluhan/Kondisi Kesehatan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::BEROBAT))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::BEROBAT))
                                ->rows(3)
                                ->maxLength(500)
                                ->validationMessages([
                                    'required' => 'Keluhan/kondisi kesehatan wajib diisi.',
                                    'max' => 'Keluhan maksimal 500 karakter.',
                                ])
                                ->helperText('Jelaskan kondisi atau keluhan kesehatan')
                                ->columnSpanFull(),

                            Select::make('data_tambahan.tujuan_berobat')
                                ->label('Tujuan Berobat')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::BEROBAT))
                                ->hint('Opsional')
                                ->options([
                                    'puskesmas' => 'Puskesmas',
                                    'rumah_sakit' => 'Rumah Sakit',
                                    'klinik' => 'Klinik',
                                    'praktek_dokter' => 'Praktek Dokter',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->native(false)
                                ->searchable()
                                ->columnSpanFull(),

                            Select::make('data_tambahan.jenis_pengobatan')
                                ->label('Jenis Pengobatan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::BEROBAT))
                                ->hint('Opsional')
                                ->options([
                                    'umum' => 'Pengobatan Umum',
                                    'bpjs' => 'BPJS/Jaminan Kesehatan',
                                    'rujukan' => 'Rujukan Rumah Sakit',
                                    'gratis' => 'Berobat Gratis',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->native(false)
                                ->searchable()
                                ->columnSpanFull(),

                            // Data Izin Keramaian - hanya muncul jika jenis = izin-keramaian
                            Select::make('data_tambahan.jenis_kegiatan')
                                ->label('Jenis Kegiatan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::IZIN_KERAMAIAN))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::IZIN_KERAMAIAN))
                                ->options([
                                    'pernikahan' => 'Pernikahan/Resepsi',
                                    'sunatan' => 'Sunatan/Khitanan',
                                    'syukuran' => 'Syukuran',
                                    'pengajian' => 'Pengajian/Tahlilan',
                                    'arisan' => 'Arisan RT/RW',
                                    'lomba' => 'Lomba/Pertandingan',
                                    'bazaar' => 'Bazaar/Pameran',
                                    'konser' => 'Konser/Pentas Seni',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->native(false)
                                ->searchable()
                                ->columnSpanFull(),

                            TextInput::make('data_tambahan.nama_kegiatan')
                                ->label('Nama Kegiatan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::IZIN_KERAMAIAN))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::IZIN_KERAMAIAN))
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Textarea::make('data_tambahan.tempat_kegiatan')
                                ->label('Tempat/Lokasi Kegiatan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::IZIN_KERAMAIAN))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::IZIN_KERAMAIAN))
                                ->rows(2)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            DatePicker::make('data_tambahan.tanggal_mulai')
                                ->label('Tanggal Mulai Kegiatan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::IZIN_KERAMAIAN))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::IZIN_KERAMAIAN))
                                ->native(false)
                                ->displayFormat('d/m/Y'),

                            DatePicker::make('data_tambahan.tanggal_selesai')
                                ->label('Tanggal Selesai Kegiatan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::IZIN_KERAMAIAN))
                                ->hint('Opsional')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->helperText('Jika kegiatan lebih dari 1 hari'),

                            TextInput::make('data_tambahan.jumlah_peserta')
                                ->label('Perkiraan Jumlah Peserta')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::IZIN_KERAMAIAN))
                                ->hint('Opsional')
                                ->numeric()
                                ->minValue(1)
                                ->validationMessages([
                                    'numeric' => 'Jumlah peserta harus berupa angka.',
                                    'min' => 'Jumlah peserta minimal 1 orang.',
                                ])
                                ->helperText('Perkiraan jumlah orang yang akan hadir'),

                            TextInput::make('data_tambahan.penanggung_jawab')
                                ->label('Penanggung Jawab Kegiatan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::IZIN_KERAMAIAN))
                                ->hint('Opsional')
                                ->maxLength(255)
                                ->helperText('Nama penanggung jawab (jika berbeda dengan pemohon)')
                                ->columnSpanFull(),

                            // Data Kredit Bank - hanya muncul jika jenis = kredit-bank
                            Select::make('data_tambahan.jenis_kredit')
                                ->label('Jenis Kredit')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::KREDIT_BANK))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::KREDIT_BANK))
                                ->options([
                                    'kpr' => 'KPR (Kredit Pemilikan Rumah)',
                                    'kendaraan' => 'Kredit Kendaraan',
                                    'usaha' => 'Kredit Usaha/Modal',
                                    'multiguna' => 'Kredit Multiguna',
                                    'pendidikan' => 'Kredit Pendidikan',
                                    'tanpa_agunan' => 'Kredit Tanpa Agunan (KTA)',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->native(false)
                                ->searchable()
                                ->columnSpanFull(),

                            TextInput::make('data_tambahan.nama_bank')
                                ->label('Nama Bank/Lembaga Keuangan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::KREDIT_BANK))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::KREDIT_BANK))
                                ->maxLength(255)
                                ->helperText('Contoh: Bank BRI, Bank BNI, Bank Mandiri, dll')
                                ->columnSpanFull(),

                            TextInput::make('data_tambahan.pekerjaan')
                                ->label('Pekerjaan/Sumber Penghasilan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::KREDIT_BANK))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::KREDIT_BANK))
                                ->maxLength(255)
                                ->columnSpanFull(),

                            TextInput::make('data_tambahan.penghasilan_perbulan')
                                ->label('Penghasilan Per Bulan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::KREDIT_BANK))
                                ->hint('Opsional')
                                ->numeric()
                                ->prefix('Rp')
                                ->validationMessages([
                                    'numeric' => 'Penghasilan harus berupa angka.',
                                ])
                                ->helperText('Penghasilan rata-rata per bulan'),

                            TextInput::make('data_tambahan.jumlah_pinjaman')
                                ->label('Jumlah Pinjaman yang Diajukan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::KREDIT_BANK))
                                ->hint('Opsional')
                                ->numeric()
                                ->prefix('Rp')
                                ->validationMessages([
                                    'numeric' => 'Jumlah pinjaman harus berupa angka.',
                                ])
                                ->helperText('Jumlah kredit yang akan diajukan'),

                            Textarea::make('data_tambahan.keterangan')
                                ->label('Keterangan Tambahan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::KREDIT_BANK))
                                ->hint('Opsional')
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('Informasi tambahan tentang kredit yang diajukan')
                                ->columnSpanFull(),

                            // Data Lainnya - hanya muncul jika jenis = lainnya
                            TextInput::make('data_tambahan.tujuan_surat')
                                ->label('Tujuan Surat')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::LAINNYA))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::LAINNYA))
                                ->maxLength(255)
                                ->helperText('Untuk keperluan apa surat ini dibuat')
                                ->columnSpanFull(),

                            Textarea::make('data_tambahan.keterangan_lengkap')
                                ->label('Keterangan Lengkap')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::LAINNYA))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratPengantar::LAINNYA))
                                ->rows(5)
                                ->maxLength(1000)
                                ->helperText('Jelaskan secara lengkap keperluan surat pengantar ini')
                                ->columnSpanFull(),
                        ]),

                    // Step 3: Dokumen & Tanda Tangan
                    Step::make('Dokumen & Tanda Tangan')
                        ->description('Upload dokumen pendukung')
                        ->icon('heroicon-o-document-arrow-up')
                        ->schema([
                            FileUpload::make('dokumen_pendukung')
                                ->label('Dokumen Pendukung')
                                ->multiple()
                                ->image()
                                ->imageEditor()
                                ->disk('public')
                                ->directory('surat-pengantar/dokumen')
                                ->visibility('public')
                                ->maxSize(2048)
                                ->maxFiles(5)
                                ->hint('Opsional')
                                ->helperText('Upload dokumen pendukung (KTP, KK, dll). Maks 5 file @ 2MB')
                                ->columnSpanFull(),

                            Select::make('kepala_desa_id')
                                ->label('Kepala Desa yang Menandatangani')
                                ->options(function (Get $get) {
                                    $desaId = $get('desa_id');
                                    if (! $desaId) {
                                        return [];
                                    }

                                    return AparatDesa::where('desa_id', $desaId)
                                        ->where('jabatan', \App\Enums\JabatanAparatDesa::KEPALA_DESA)
                                        ->where('status', 'aktif')
                                        ->pluck('nama_lengkap', 'id');
                                })
                                ->searchable()
                                ->preload()
                                ->hint('Opsional')
                                ->helperText('Pilih Kepala Desa yang akan menandatangani surat')
                                ->columnSpanFull(),

                            DatePicker::make('tanggal_surat')
                                ->label('Tanggal Surat')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->default(now())
                                ->hint('Otomatis')
                                ->helperText('Tanggal pembuatan surat'),

                            Textarea::make('keterangan')
                                ->label('Keterangan Tambahan')
                                ->rows(3)
                                ->maxLength(1000)
                                ->hint('Opsional')
                                ->helperText('Catatan atau keterangan tambahan')
                                ->columnSpanFull(),
                        ]),
                ])
                    ->columnSpanFull()
                    ->persistStepInQueryString(),
            ]);
    }
}
