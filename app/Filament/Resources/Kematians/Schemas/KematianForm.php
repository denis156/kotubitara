<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kematians\Schemas;

use App\Enums\HubunganPelapor;
use App\Helpers\DesaFieldHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KematianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kematian')
                    ->description('Data penduduk yang meninggal dan detail kematian.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('penduduk_id')
                            ->label('Penduduk yang Meninggal')
                            ->relationship('penduduk', 'nama_lengkap')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih penduduk yang meninggal dunia.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Penduduk wajib dipilih.',
                            ]),

                        Select::make('desa_id')
                            ->label('Desa')
                            ->relationship('desa', 'nama_desa')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(fn () => DesaFieldHelper::getDefaultDesaId())
                            ->disabled(fn () => DesaFieldHelper::shouldDisableDesaField())
                            ->dehydrated()
                            ->helperText('Desa tempat pencatatan kematian.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Desa wajib dipilih.',
                            ]),

                        DatePicker::make('tanggal_meninggal')
                            ->label('Tanggal Meninggal')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->helperText('Tanggal penduduk meninggal dunia.')
                            ->validationMessages([
                                'required' => 'Tanggal meninggal wajib diisi.',
                                'before_or_equal' => 'Tanggal meninggal tidak boleh di masa depan.',
                            ]),

                        TimePicker::make('waktu_meninggal')
                            ->label('Waktu Meninggal')
                            ->native(false)
                            ->seconds(false)
                            ->hint('Opsional')
                            ->helperText('Jam dan menit saat meninggal.')
                            ->validationMessages([
                                'date_format' => 'Format waktu tidak valid.',
                            ]),

                        TextInput::make('tempat_meninggal')
                            ->label('Tempat Meninggal')
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Lokasi atau tempat penduduk meninggal (cth: Rumah, Rumah Sakit).')
                            ->validationMessages([
                                'max' => 'Tempat meninggal tidak boleh lebih dari 255 karakter.',
                            ]),

                        TextInput::make('sebab_kematian')
                            ->label('Sebab Kematian')
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Penyebab kematian (cth: Sakit, Kecelakaan, Usia Lanjut).')
                            ->validationMessages([
                                'max' => 'Sebab kematian tidak boleh lebih dari 255 karakter.',
                            ]),

                        TextInput::make('tempat_pemakaman')
                            ->label('Tempat Pemakaman')
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Lokasi pemakaman (cth: TPU, Makam Keluarga).')
                            ->validationMessages([
                                'max' => 'Tempat pemakaman tidak boleh lebih dari 255 karakter.',
                            ]),

                        DatePicker::make('tanggal_pemakaman')
                            ->label('Tanggal Pemakaman')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->hint('Opsional')
                            ->helperText('Tanggal dilaksanakan pemakaman.')
                            ->validationMessages([
                                'date' => 'Format tanggal tidak valid.',
                            ]),
                    ]),

                Section::make('Data Pelapor')
                    ->description('Informasi pelapor kematian.')
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
                            ->helperText('Hubungan pelapor dengan yang meninggal.')
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

                Section::make('Dokumen Pendukung')
                    ->description('Dokumen pendukung kematian.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('foto_surat_rs')
                            ->label('Foto Surat Keterangan Dokter/RS')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('kematian/surat-dokter')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->hint('Opsional')
                            ->helperText('Upload foto surat keterangan kematian dari dokter/RS (maks. 2MB).')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Ukuran file tidak boleh lebih dari 2MB.',
                            ]),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->maxLength(1000)
                            ->rows(3)
                            ->hint('Opsional')
                            ->helperText('Catatan atau keterangan tambahan terkait kematian.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Keterangan tidak boleh lebih dari 1000 karakter.',
                            ]),
                    ]),
            ]);
    }
}
