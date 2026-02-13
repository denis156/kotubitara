<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas;

use App\Enums\JenisSuratKeterangan;
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

class SuratKeteranganForm
{
    protected static function isJenisSurat(mixed $value, JenisSuratKeterangan $jenis): bool
    {
        // Handle null
        if ($value === null) {
            return false;
        }

        // If it's already an enum instance
        if ($value instanceof JenisSuratKeterangan) {
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
                    // Step 1: Pilih Jenis Surat & Data Pemohon
                    Step::make('Jenis Surat & Pemohon')
                        ->description('Pilih jenis surat dan data pemohon')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Select::make('jenis_surat')
                                ->label('Jenis Surat Keterangan')
                                ->options(JenisSuratKeterangan::class)
                                ->required()
                                ->live()
                                ->native(false)
                                ->searchable()
                                ->helperText('Pilih jenis surat keterangan yang akan dibuat')
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

                            TextInput::make('keperluan')
                                ->label('Keperluan')
                                ->maxLength(255)
                                ->hint('Opsional')
                                ->helperText('Untuk apa surat ini dibuat (contoh: Melamar pekerjaan, NPWP, dll)')
                                ->columnSpanFull(),
                        ]),

                    // Step 2: Data Spesifik (Dynamic based on jenis_surat)
                    Step::make('Data Spesifik')
                        ->description('Isi data sesuai jenis surat')
                        ->icon('heroicon-o-pencil-square')
                        ->schema([

                            // Data Domisili - hanya muncul jika jenis = domisili
                            TextInput::make('data_domisili.rt')
                                ->label('RT')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::DOMISILI))
                                ->hint('Opsional')
                                ->maxLength(10),

                            TextInput::make('data_domisili.rw')
                                ->label('RW')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::DOMISILI))
                                ->hint('Opsional')
                                ->maxLength(10),

                            Textarea::make('data_domisili.alamat_lengkap')
                                ->label('Alamat Lengkap')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::DOMISILI))
                                ->hint('Opsional')
                                ->rows(3)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            DatePicker::make('data_domisili.sejak_tinggal')
                                ->label('Tinggal Sejak')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::DOMISILI))
                                ->hint('Opsional')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->maxDate(now())
                                ->validationMessages([
                                    'before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
                                ])
                                ->helperText('Sejak kapan tinggal di alamat ini')
                                ->columnSpanFull(),

                            // Data Usaha - hanya muncul jika jenis = usaha
                            TextInput::make('data_usaha.nama_usaha')
                                ->label('Nama Usaha')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::USAHA))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::USAHA))
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Select::make('data_usaha.jenis_usaha')
                                ->label('Jenis Usaha')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::USAHA))
                                ->hint('Opsional')
                                ->options([
                                    'warung_toko' => 'Warung/Toko',
                                    'kuliner' => 'Kuliner (Makanan/Minuman)',
                                    'jasa' => 'Jasa',
                                    'pertanian' => 'Pertanian',
                                    'peternakan' => 'Peternakan',
                                    'kerajinan' => 'Kerajinan',
                                    'perdagangan' => 'Perdagangan',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->native(false)
                                ->searchable()
                                ->columnSpanFull(),

                            Textarea::make('data_usaha.alamat_usaha')
                                ->label('Alamat Usaha')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::USAHA))
                                ->hint('Opsional')
                                ->rows(2)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            TextInput::make('data_usaha.modal')
                                ->label('Modal Usaha')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::USAHA))
                                ->hint('Opsional')
                                ->numeric()
                                ->prefix('Rp')
                                ->helperText('Modal awal usaha'),

                            TextInput::make('data_usaha.jumlah_karyawan')
                                ->label('Jumlah Karyawan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::USAHA))
                                ->hint('Opsional')
                                ->numeric()
                                ->minValue(0)
                                ->validationMessages([
                                    'numeric' => 'Jumlah karyawan harus berupa angka.',
                                    'min' => 'Jumlah karyawan tidak boleh kurang dari 0.',
                                ])
                                ->helperText('Jumlah karyawan yang dipekerjakan'),

                            // Data Ekonomi - hanya muncul jika jenis = tidak-mampu
                            TextInput::make('data_ekonomi.penghasilan_perbulan')
                                ->label('Penghasilan Per Bulan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::TIDAK_MAMPU))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::TIDAK_MAMPU))
                                ->numeric()
                                ->prefix('Rp')
                                ->validationMessages([
                                    'required' => 'Penghasilan per bulan wajib diisi.',
                                    'numeric' => 'Penghasilan harus berupa angka.',
                                ])
                                ->helperText('Total penghasilan keluarga per bulan'),

                            TextInput::make('data_ekonomi.jumlah_tanggungan')
                                ->label('Jumlah Tanggungan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::TIDAK_MAMPU))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::TIDAK_MAMPU))
                                ->numeric()
                                ->minValue(0)
                                ->validationMessages([
                                    'required' => 'Jumlah tanggungan wajib diisi.',
                                    'numeric' => 'Jumlah tanggungan harus berupa angka.',
                                    'min' => 'Jumlah tanggungan tidak boleh kurang dari 0.',
                                ])
                                ->helperText('Jumlah anggota keluarga yang menjadi tanggungan'),

                            Textarea::make('data_ekonomi.kondisi_rumah')
                                ->label('Kondisi Rumah')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::TIDAK_MAMPU))
                                ->hint('Opsional')
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('Deskripsi kondisi tempat tinggal')
                                ->columnSpanFull(),

                            Textarea::make('data_ekonomi.alasan')
                                ->label('Alasan Permohonan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::TIDAK_MAMPU))
                                ->hint('Opsional')
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('Alasan memerlukan surat keterangan tidak mampu')
                                ->columnSpanFull(),

                            // Data Pernikahan (Belum Menikah) - hanya muncul jika jenis = belum-menikah
                            TextInput::make('data_pernikahan.tempat_lahir')
                                ->label('Tempat Lahir')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::BELUM_MENIKAH))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::BELUM_MENIKAH))
                                ->maxLength(255),

                            DatePicker::make('data_pernikahan.tanggal_lahir')
                                ->label('Tanggal Lahir')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::BELUM_MENIKAH))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::BELUM_MENIKAH))
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->maxDate(now()),

                            TextInput::make('data_pernikahan.pekerjaan')
                                ->label('Pekerjaan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::BELUM_MENIKAH))
                                ->hint('Opsional')
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Textarea::make('data_pernikahan.alamat_lengkap')
                                ->label('Alamat Lengkap')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::BELUM_MENIKAH))
                                ->hint('Opsional')
                                ->rows(3)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            // Data Pernikahan (Sudah Menikah) - hanya muncul jika jenis = sudah-menikah
                            TextInput::make('data_pernikahan.nama_pasangan')
                                ->label('Nama Pasangan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::SUDAH_MENIKAH))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::SUDAH_MENIKAH))
                                ->maxLength(255)
                                ->columnSpanFull(),

                            DatePicker::make('data_pernikahan.tanggal_menikah')
                                ->label('Tanggal Menikah')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::SUDAH_MENIKAH))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::SUDAH_MENIKAH))
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->maxDate(now())
                                ->columnSpanFull(),

                            TextInput::make('data_pernikahan.tempat_menikah')
                                ->label('Tempat Menikah')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::SUDAH_MENIKAH))
                                ->hint('Opsional')
                                ->maxLength(255),

                            TextInput::make('data_pernikahan.nomor_kutipan_akta_nikah')
                                ->label('Nomor Kutipan Akta Nikah')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::SUDAH_MENIKAH))
                                ->hint('Opsional')
                                ->maxLength(255),

                            // Data Penghasilan - hanya muncul jika jenis = penghasilan
                            TextInput::make('data_ekonomi.pekerjaan')
                                ->label('Pekerjaan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::PENGHASILAN))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::PENGHASILAN))
                                ->maxLength(255)
                                ->columnSpanFull(),

                            TextInput::make('data_ekonomi.nama_perusahaan')
                                ->label('Nama Perusahaan/Instansi')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::PENGHASILAN))
                                ->hint('Opsional')
                                ->maxLength(255)
                                ->helperText('Jika bekerja di perusahaan/instansi')
                                ->columnSpanFull(),

                            TextInput::make('data_ekonomi.penghasilan_perbulan')
                                ->label('Penghasilan Per Bulan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::PENGHASILAN))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::PENGHASILAN))
                                ->numeric()
                                ->prefix('Rp')
                                ->validationMessages([
                                    'required' => 'Penghasilan per bulan wajib diisi.',
                                    'numeric' => 'Penghasilan harus berupa angka.',
                                ])
                                ->helperText('Penghasilan rata-rata per bulan'),

                            TextInput::make('data_ekonomi.penghasilan_pertahun')
                                ->label('Penghasilan Per Tahun')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::PENGHASILAN))
                                ->hint('Opsional')
                                ->numeric()
                                ->prefix('Rp')
                                ->helperText('Total penghasilan per tahun'),

                            // Data Ahli Waris - hanya muncul jika jenis = ahli-waris
                            TextInput::make('data_ahli_waris.nama_pewaris')
                                ->label('Nama Pewaris (Yang Meninggal)')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::AHLI_WARIS))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::AHLI_WARIS))
                                ->maxLength(255)
                                ->columnSpanFull(),

                            TextInput::make('data_ahli_waris.nik_pewaris')
                                ->label('NIK Pewaris')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::AHLI_WARIS))
                                ->hint('Opsional')
                                ->maxLength(16)
                                ->length(16)
                                ->validationMessages([
                                    'length' => 'NIK harus tepat 16 digit.',
                                ]),

                            DatePicker::make('data_ahli_waris.tanggal_meninggal')
                                ->label('Tanggal Meninggal')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::AHLI_WARIS))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::AHLI_WARIS))
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->maxDate(now()),

                            Select::make('data_ahli_waris.hubungan_dengan_pewaris')
                                ->label('Hubungan dengan Pewaris')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::AHLI_WARIS))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::AHLI_WARIS))
                                ->options([
                                    'anak_kandung' => 'Anak Kandung',
                                    'anak_tiri' => 'Anak Tiri',
                                    'istri' => 'Istri',
                                    'suami' => 'Suami',
                                    'ayah' => 'Ayah',
                                    'ibu' => 'Ibu',
                                    'saudara_kandung' => 'Saudara Kandung',
                                    'cucu' => 'Cucu',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->native(false)
                                ->searchable()
                                ->columnSpanFull(),

                            Textarea::make('data_ahli_waris.keterangan_harta')
                                ->label('Keterangan Harta Warisan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::AHLI_WARIS))
                                ->hint('Opsional')
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('Deskripsi harta yang diwariskan')
                                ->columnSpanFull(),

                            // Data Kehilangan - hanya muncul jika jenis = kehilangan
                            Select::make('data_tambahan.jenis_barang_hilang')
                                ->label('Jenis Barang yang Hilang')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KEHILANGAN))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KEHILANGAN))
                                ->options([
                                    'ktp' => 'KTP',
                                    'kk' => 'Kartu Keluarga',
                                    'sim' => 'SIM',
                                    'stnk' => 'STNK',
                                    'ijazah' => 'Ijazah',
                                    'sertifikat_tanah' => 'Sertifikat Tanah',
                                    'bpkb' => 'BPKB',
                                    'dompet' => 'Dompet',
                                    'handphone' => 'Handphone',
                                    'sepeda_motor' => 'Sepeda Motor',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->native(false)
                                ->searchable()
                                ->columnSpanFull(),

                            TextInput::make('data_tambahan.nama_barang')
                                ->label('Nama/Merk Barang')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KEHILANGAN))
                                ->hint('Opsional')
                                ->maxLength(255)
                                ->helperText('Untuk kendaraan: merk & tipe, untuk dokumen: nomor dokumen')
                                ->columnSpanFull(),

                            DatePicker::make('data_tambahan.tanggal_kehilangan')
                                ->label('Tanggal Kehilangan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KEHILANGAN))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KEHILANGAN))
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->maxDate(now())
                                ->columnSpanFull(),

                            Textarea::make('data_tambahan.tempat_kehilangan')
                                ->label('Tempat Kehilangan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KEHILANGAN))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KEHILANGAN))
                                ->rows(2)
                                ->maxLength(500)
                                ->helperText('Lokasi atau tempat terakhir kali barang terlihat')
                                ->columnSpanFull(),

                            Textarea::make('data_tambahan.kronologi')
                                ->label('Kronologi Kejadian')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KEHILANGAN))
                                ->rows(4)
                                ->maxLength(1000)
                                ->helperText('Ceritakan bagaimana kejadian kehilangan tersebut')
                                ->columnSpanFull(),

                            // Data Janda/Duda - hanya muncul jika jenis = janda-duda
                            Select::make('data_pernikahan.status')
                                ->label('Status')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::JANDA_DUDA))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::JANDA_DUDA))
                                ->options([
                                    'janda' => 'Janda',
                                    'duda' => 'Duda',
                                ])
                                ->native(false)
                                ->columnSpanFull(),

                            TextInput::make('data_pernikahan.nama_pasangan_almarhum')
                                ->label('Nama Pasangan (Almarhum/Almarhumah)')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::JANDA_DUDA))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::JANDA_DUDA))
                                ->maxLength(255)
                                ->columnSpanFull(),

                            DatePicker::make('data_pernikahan.tanggal_meninggal_pasangan')
                                ->label('Tanggal Pasangan Meninggal')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::JANDA_DUDA))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::JANDA_DUDA))
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->maxDate(now())
                                ->columnSpanFull(),

                            DatePicker::make('data_pernikahan.tanggal_menikah')
                                ->label('Tanggal Menikah (Dahulu)')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::JANDA_DUDA))
                                ->hint('Opsional')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->maxDate(now())
                                ->helperText('Tanggal pernikahan dengan pasangan yang telah meninggal')
                                ->columnSpanFull(),

                            // Data Kelakuan Baik - hanya muncul jika jenis = kelakuan-baik
                            TextInput::make('data_tambahan.pekerjaan')
                                ->label('Pekerjaan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KELAKUAN_BAIK))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KELAKUAN_BAIK))
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Textarea::make('data_tambahan.alamat_lengkap')
                                ->label('Alamat Lengkap')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KELAKUAN_BAIK))
                                ->required(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KELAKUAN_BAIK))
                                ->rows(3)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            Select::make('data_tambahan.keperluan_surat')
                                ->label('Keperluan Surat')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KELAKUAN_BAIK))
                                ->hint('Opsional')
                                ->options([
                                    'melamar_pekerjaan' => 'Melamar Pekerjaan',
                                    'cpns_tni_polri' => 'CPNS/TNI/Polri',
                                    'beasiswa' => 'Beasiswa',
                                    'keperluan_sekolah' => 'Keperluan Sekolah/Kuliah',
                                    'organisasi' => 'Keperluan Organisasi',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->native(false)
                                ->searchable()
                                ->helperText('Untuk apa surat ini diperlukan')
                                ->columnSpanFull(),

                            Textarea::make('data_tambahan.keterangan_kelakuan')
                                ->label('Keterangan Kelakuan')
                                ->visible(fn (Get $get) => self::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KELAKUAN_BAIK))
                                ->rows(4)
                                ->maxLength(1000)
                                ->default('Yang bersangkutan berkelakuan baik, tidak pernah terlibat tindak pidana, dan merupakan warga yang taat pada peraturan yang berlaku.')
                                ->helperText('Deskripsi kelakuan yang bersangkutan')
                                ->columnSpanFull(),
                        ]),

                    // Step 3: Tanda Tangan & Dokumen
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
                                ->directory('surat-keterangan/dokumen')
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
                    ->columnSpanFull(),
            ]);
    }
}
