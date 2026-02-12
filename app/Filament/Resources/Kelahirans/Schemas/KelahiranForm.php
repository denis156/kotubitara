<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kelahirans\Schemas;

use App\Enums\HubunganPelapor;
use App\Enums\JabatanAparat;
use App\Enums\JenisKelamin;
use App\Helpers\FilamentHelper;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class KelahiranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Bayi')
                    ->description('Data identitas dan kelahiran bayi.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('desa_id')
                            ->label('Desa')
                            ->relationship('desa', 'nama_desa')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(fn () => FilamentHelper::getDefaultDesaId())
                            ->disabled(fn () => FilamentHelper::shouldDisableDesaField())
                            ->dehydrated()
                            ->helperText('Desa tempat kelahiran didaftarkan.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Desa wajib dipilih.',
                            ]),

                        TextInput::make('nama_bayi')
                            ->label('Nama Bayi')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nama lengkap bayi.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Nama bayi wajib diisi.',
                                'max' => 'Nama bayi tidak boleh lebih dari 255 karakter.',
                            ]),

                        TextInput::make('nik_bayi')
                            ->label('NIK Bayi')
                            ->length(16)
                            ->unique(ignoreRecord: true)
                            ->hint('Opsional')
                            ->helperText('NIK bayi 16 digit jika sudah tersedia.')
                            ->validationMessages([
                                'digits' => 'NIK harus tepat 16 digit.',
                                'unique' => 'NIK sudah terdaftar.',
                            ]),

                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options(JenisKelamin::class)
                            ->required()
                            ->native(false)
                            ->helperText('Pilih jenis kelamin bayi.')
                            ->validationMessages([
                                'required' => 'Jenis kelamin wajib dipilih.',
                            ]),
                    ]),

                Section::make('Waktu & Tempat Kelahiran')
                    ->description('Detail waktu, tempat, dan ukuran kelahiran.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->helperText('Tanggal kelahiran bayi.')
                            ->validationMessages([
                                'required' => 'Tanggal lahir wajib diisi.',
                                'before_or_equal' => 'Tanggal lahir tidak boleh di masa depan.',
                            ]),

                        TimePicker::make('waktu_lahir')
                            ->label('Waktu Lahir')
                            ->native(false)
                            ->seconds(false)
                            ->hint('Opsional')
                            ->helperText('Waktu kelahiran (jam:menit).')
                            ->validationMessages([
                                'date_format' => 'Format waktu tidak valid.',
                            ]),

                        TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Lokasi kelahiran (cth: RS, Puskesmas, Rumah).')
                            ->validationMessages([
                                'required' => 'Tempat lahir wajib diisi.',
                                'max' => 'Tempat lahir tidak boleh lebih dari 255 karakter.',
                            ]),

                        TextInput::make('berat_lahir')
                            ->label('Berat Lahir (kg)')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(10)
                            ->hint('Opsional')
                            ->helperText('Berat bayi dalam kilogram (cth: 3.2).')
                            ->validationMessages([
                                'numeric' => 'Berat lahir harus berupa angka.',
                                'min' => 'Berat lahir tidak boleh kurang dari 0.',
                                'max' => 'Berat lahir tidak boleh lebih dari 10 kg.',
                            ]),

                        TextInput::make('panjang_lahir')
                            ->label('Panjang Lahir (cm)')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->hint('Opsional')
                            ->helperText('Panjang bayi dalam sentimeter (cth: 50).')
                            ->validationMessages([
                                'numeric' => 'Panjang lahir harus berupa angka.',
                                'min' => 'Panjang lahir tidak boleh kurang dari 0.',
                                'max' => 'Panjang lahir tidak boleh lebih dari 100 cm.',
                            ]),
                    ]),

                Section::make('Data Orang Tua')
                    ->description('Informasi ayah dan ibu dari bayi.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('ayah_id')
                            ->label('Ayah')
                            ->relationship('ayah', 'nama_lengkap')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih ayah dari data penduduk yang terdaftar.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Ayah wajib dipilih.',
                            ]),

                        Select::make('ibu_id')
                            ->label('Ibu')
                            ->relationship('ibu', 'nama_lengkap')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih ibu dari data penduduk yang terdaftar.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Ibu wajib dipilih.',
                            ]),
                    ]),

                Section::make('Data Pelapor')
                    ->description('Informasi pelapor kelahiran.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('nama_pelapor')
                            ->label('Nama Pelapor')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nama lengkap pelapor.')
                            ->validationMessages([
                                'required' => 'Nama pelapor wajib diisi.',
                                'max' => 'Nama pelapor tidak boleh lebih dari 255 karakter.',
                            ]),

                        TextInput::make('nik_pelapor')
                            ->label('NIK Pelapor')
                            ->length(16)
                            ->hint('Opsional')
                            ->helperText('NIK pelapor 16 digit.')
                            ->validationMessages([
                                'digits' => 'NIK harus tepat 16 digit.',
                            ]),

                        Select::make('hubungan_pelapor')
                            ->label('Hubungan Pelapor')
                            ->options(HubunganPelapor::class)
                            ->required()
                            ->native(false)
                            ->helperText('Hubungan pelapor dengan bayi.')
                            ->validationMessages([
                                'required' => 'Hubungan pelapor wajib dipilih.',
                            ]),

                        Textarea::make('alamat_pelapor')
                            ->label('Alamat Pelapor')
                            ->maxLength(500)
                            ->rows(2)
                            ->hint('Opsional')
                            ->helperText('Alamat lengkap pelapor.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Alamat pelapor tidak boleh lebih dari 500 karakter.',
                            ]),

                        TextInput::make('telepon_pelapor')
                            ->label('Telepon Pelapor')
                            ->tel()
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Nomor telepon pelapor.')
                            ->validationMessages([
                                'max' => 'Nomor telepon tidak boleh lebih dari 255 karakter.',
                            ]),
                    ]),

                Section::make('Tanda Tangan & Dokumen')
                    ->description('Tanda tangan pelapor dan dokumen pendukung.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        SignaturePad::make('ttd_pelapor')
                            ->label('Tanda Tangan Digital Pelapor')
                            ->backgroundColor('rgba(250, 250, 250, 1)')
                            ->backgroundColorOnDark('rgba(30, 30, 30, 1)')
                            ->exportBackgroundColor('rgb(255, 255, 255)')
                            ->penColor('#000')
                            ->penColorOnDark('#fff')
                            ->exportPenColor('rgb(0, 0, 0)')
                            ->dotSize(2.0)
                            ->lineMinWidth(0.5)
                            ->lineMaxWidth(2.5)
                            ->throttle(16)
                            ->velocityFilterWeight(0.7)
                            ->minDistance(5)
                            ->confirmable()
                            ->clearable()
                            ->undoable()
                            ->required(false)
                            ->hint('Opsional')
                            ->helperText('Tanda tangan di area kotak dengan mouse atau touchscreen.')
                            ->columnSpanFull()
                            ->clearAction(fn (Action $action) => $action->button())
                            ->undoAction(fn (Action $action) => $action->button()->icon('heroicon-o-arrow-uturn-left'))
                            ->doneAction(fn (Action $action) => $action->button()->icon('heroicon-o-check-circle')),

                        FileUpload::make('foto_ttd_pelapor')
                            ->label('Foto Tanda Tangan Pelapor')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('kelahiran/tanda-tangan')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->hint('Opsional')
                            ->helperText('Upload foto tanda tangan pelapor sebagai alternatif (maks. 2MB).')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Ukuran file tidak boleh lebih dari 2MB.',
                            ]),

                        FileUpload::make('foto_surat_rs')
                            ->label('Foto Surat Keterangan Lahir dari RS/Bidan/Puskesmas')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('kelahiran/surat-rs')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->hint('Opsional')
                            ->helperText('Upload foto surat keterangan lahir dari RS/Bidan/Puskesmas (maks. 2MB).')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Ukuran file tidak boleh lebih dari 2MB.',
                            ]),

                        Select::make('kepala_desa_id')
                            ->label('Kepala Desa yang Menandatangani')
                            ->relationship(
                                'kepalaDesa',
                                'nama_lengkap',
                                fn ($query, Get $get) => $query
                                    ->where('jabatan', JabatanAparat::KEPALA_DESA)
                                    ->where('status', 'aktif')
                                    ->when(
                                        $get('desa_id'),
                                        fn ($q, $desaId) => $q->where('desa_id', $desaId)
                                    )
                            )
                            ->searchable()
                            ->preload()
                            ->hint('Opsional')
                            ->helperText('Pilih Kepala Desa yang akan menandatangani surat.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Nomor Surat & Keterangan')
                    ->description('Nomor surat pengantar kelahiran dan catatan tambahan.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('no_surat_kelahiran')
                            ->label('Nomor Surat Pengantar')
                            ->maxLength(255)
                            ->readOnly()
                            ->dehydrated()
                            ->hint('Otomatis')
                            ->helperText('Nomor surat dibuat otomatis: SP/LHR/YYYY/MM/XXXXX')
                            ->placeholder('Akan dibuat otomatis...')
                            ->validationMessages([
                                'max' => 'Nomor surat tidak boleh lebih dari 255 karakter.',
                            ]),

                        DatePicker::make('tanggal_surat')
                            ->label('Tanggal Surat')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->hint('Opsional')
                            ->helperText('Tanggal pembuatan surat pengantar.')
                            ->validationMessages([
                                'date' => 'Format tanggal tidak valid.',
                            ]),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->maxLength(1000)
                            ->hint('Opsional')
                            ->helperText('Catatan atau keterangan tambahan.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Keterangan tidak boleh lebih dari 1000 karakter.',
                            ]),
                    ]),
            ]);
    }
}
