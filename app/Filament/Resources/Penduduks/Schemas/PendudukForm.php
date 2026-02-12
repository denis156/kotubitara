<?php

declare(strict_types=1);

namespace App\Filament\Resources\Penduduks\Schemas;

use App\Enums\Agama;
use App\Enums\HubunganKeluarga;
use App\Enums\JenisKelamin;
use App\Enums\Kewarganegaraan;
use App\Enums\StatusPerkawinan;
use App\Helpers\DesaFieldHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PendudukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Penduduk')
                    ->description('Data identitas dan dokumen kependudukan.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('nik')
                            ->label('NIK (Nomor Induk Kependudukan)')
                            ->required()
                            ->length(16)
                            ->unique(ignoreRecord: true)
                            ->helperText('NIK harus 16 digit sesuai KTP.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'NIK wajib diisi.',
                                'digits' => 'NIK harus tepat 16 digit.',
                                'unique' => 'NIK sudah terdaftar. Gunakan NIK lain.',
                            ]),

                        Select::make('kartu_keluarga_id')
                            ->label('Kartu Keluarga')
                            ->relationship('kartuKeluarga', 'no_kk')
                            ->disabled()
                            ->hint('Otomatis')
                            ->helperText('Akan terisi otomatis saat penduduk ditambahkan ke Kartu Keluarga.')
                            ->placeholder('Belum terdaftar dalam KK')
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
                            ->helperText('Pilih desa tempat penduduk terdaftar.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Desa wajib dipilih.',
                            ]),

                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nama lengkap sesuai KTP/Akta Kelahiran.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Nama lengkap wajib diisi.',
                                'max' => 'Nama tidak boleh lebih dari 255 karakter.',
                            ]),
                    ]),

                Section::make('Data Pribadi')
                    ->description('Informasi pribadi dan kelahiran.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options(JenisKelamin::class)
                            ->required()
                            ->native(false)
                            ->helperText('Pilih jenis kelamin.')
                            ->validationMessages([
                                'required' => 'Jenis kelamin wajib dipilih.',
                            ]),

                        TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Tempat lahir sesuai KTP.')
                            ->validationMessages([
                                'required' => 'Tempat lahir wajib diisi.',
                                'max' => 'Tempat lahir tidak boleh lebih dari 255 karakter.',
                            ]),

                        DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->helperText('Tanggal lahir sesuai KTP.')
                            ->validationMessages([
                                'required' => 'Tanggal lahir wajib diisi.',
                                'before_or_equal' => 'Tanggal lahir tidak boleh di masa depan.',
                            ]),

                        Select::make('agama')
                            ->label('Agama')
                            ->options(Agama::class)
                            ->required()
                            ->native(false)
                            ->helperText('Pilih agama yang dianut.')
                            ->validationMessages([
                                'required' => 'Agama wajib dipilih.',
                            ]),

                        Select::make('kewarganegaraan')
                            ->label('Kewarganegaraan')
                            ->options(Kewarganegaraan::class)
                            ->default('wni')
                            ->required()
                            ->native(false)
                            ->helperText('Pilih status kewarganegaraan.')
                            ->validationMessages([
                                'required' => 'Kewarganegaraan wajib dipilih.',
                            ]),
                    ]),

                Section::make('Status & Hubungan Keluarga')
                    ->description('Status perkawinan dan hubungan dalam keluarga.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status_perkawinan')
                            ->label('Status Perkawinan')
                            ->options(StatusPerkawinan::class)
                            ->required()
                            ->native(false)
                            ->helperText('Pilih status perkawinan saat ini.')
                            ->validationMessages([
                                'required' => 'Status perkawinan wajib dipilih.',
                            ]),

                        Select::make('hubungan_keluarga')
                            ->label('Hubungan dalam Keluarga')
                            ->options(HubunganKeluarga::class)
                            ->required()
                            ->native(false)
                            ->helperText('Hubungan dengan kepala keluarga.')
                            ->validationMessages([
                                'required' => 'Hubungan dalam keluarga wajib dipilih.',
                            ]),

                        TextInput::make('nama_ayah')
                            ->label('Nama Ayah')
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Nama lengkap ayah kandung.')
                            ->validationMessages([
                                'max' => 'Nama ayah tidak boleh lebih dari 255 karakter.',
                            ]),

                        TextInput::make('nama_ibu')
                            ->label('Nama Ibu')
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Nama lengkap ibu kandung.')
                            ->validationMessages([
                                'max' => 'Nama ibu tidak boleh lebih dari 255 karakter.',
                            ]),
                    ]),

                Section::make('Pekerjaan & Pendidikan')
                    ->description('Informasi pekerjaan dan pendidikan terakhir.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('pekerjaan')
                            ->label('Pekerjaan')
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Pekerjaan/profesi saat ini (cth: Petani, PNS, Wiraswasta).')
                            ->validationMessages([
                                'max' => 'Pekerjaan tidak boleh lebih dari 255 karakter.',
                            ]),

                        TextInput::make('pendidikan')
                            ->label('Pendidikan Terakhir')
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Pendidikan terakhir yang ditempuh (cth: SD, SMP, SMA, S1).')
                            ->validationMessages([
                                'max' => 'Pendidikan tidak boleh lebih dari 255 karakter.',
                            ]),
                    ]),
            ]);
    }
}
