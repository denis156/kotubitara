<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kecamatans\Schemas;

use App\Services\ApiWilayahService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class KecamatanForm
{
    public static function configure(Schema $schema): Schema
    {
        $apiWilayah = app(ApiWilayahService::class);

        return $schema
            ->components([
                Section::make('Informasi Wilayah')
                    ->description('Pilih wilayah administrasi kecamatan dari API Wilayah Indonesia.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('nama_provinsi')
                            ->label('Provinsi')
                            ->required()
                            ->searchable()
                            ->options(fn () => $apiWilayah->getProvinces()->pluck('name', 'name')->toArray())
                            ->default('SULAWESI TENGGARA')
                            ->afterStateUpdated(function (callable $set) {
                                $set('nama_kabupaten', null);
                                $set('nama_kecamatan', null);
                            })
                            ->live()
                            ->disabled(fn () => ! Auth::user()?->isSuperAdmin())
                            ->dehydrated(fn () => Auth::user()?->isSuperAdmin())
                            ->hint(fn () => ! Auth::user()?->isSuperAdmin() ? 'Otomatis' : null)
                            ->helperText('Pilih provinsi tempat kecamatan berada.')
                            ->validationMessages([
                                'required' => 'Provinsi wajib dipilih.',
                            ]),

                        Select::make('nama_kabupaten')
                            ->label('Kabupaten/Kota')
                            ->required()
                            ->searchable()
                            ->options(function (Get $get) use ($apiWilayah) {
                                $namaProvinsi = $get('nama_provinsi');
                                if ($namaProvinsi) {
                                    $provinces = $apiWilayah->getProvinces();
                                    $province = $provinces->firstWhere('name', $namaProvinsi);
                                    if ($province) {
                                        return $apiWilayah->getRegencies($province['id'])->pluck('name', 'name')->toArray();
                                    }
                                }

                                return [];
                            })
                            ->default('KABUPATEN KONAWE')
                            ->afterStateUpdated(function (callable $set) {
                                $set('nama_kecamatan', null);
                            })
                            ->live()
                            ->disabled(fn () => ! Auth::user()?->isSuperAdmin())
                            ->dehydrated(fn () => Auth::user()?->isSuperAdmin())
                            ->hint(fn () => ! Auth::user()?->isSuperAdmin() ? 'Otomatis' : null)
                            ->helperText('Pilih kabupaten/kota sesuai provinsi di atas.')
                            ->validationMessages([
                                'required' => 'Kabupaten/Kota wajib dipilih.',
                            ]),

                        Select::make('nama_kecamatan')
                            ->label('Kecamatan')
                            ->required()
                            ->searchable()
                            ->options(function (Get $get) use ($apiWilayah) {
                                $namaProvinsi = $get('nama_provinsi');
                                $namaKabupaten = $get('nama_kabupaten');

                                if ($namaProvinsi && $namaKabupaten) {
                                    $provinces = $apiWilayah->getProvinces();
                                    $province = $provinces->firstWhere('name', $namaProvinsi);

                                    if ($province) {
                                        $regencies = $apiWilayah->getRegencies($province['id']);
                                        $regency = $regencies->firstWhere('name', $namaKabupaten);

                                        if ($regency) {
                                            return $apiWilayah->getDistricts($regency['id'])->pluck('name', 'name')->toArray();
                                        }
                                    }
                                }

                                return [];
                            })
                            ->default('WONGGEDUKU BARAT')
                            ->disabled(fn () => ! Auth::user()?->isSuperAdmin())
                            ->dehydrated(fn () => Auth::user()?->isSuperAdmin())
                            ->hint(fn () => ! Auth::user()?->isSuperAdmin() ? 'Otomatis' : null)
                            ->helperText('Pilih kecamatan sesuai kabupaten/kota di atas.')
                            ->validationMessages([
                                'required' => 'Kecamatan wajib dipilih.',
                            ]),
                    ]),

                Section::make('Informasi Kontak')
                    ->description('Lengkapi data kontak resmi kantor kecamatan.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('alamat')
                            ->label('Alamat Kantor Kecamatan')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull()
                            ->hint('Opsional')
                            ->helperText('Alamat lengkap kantor kecamatan.')
                            ->validationMessages([
                                'max' => 'Alamat tidak boleh lebih dari 500 karakter.',
                            ]),

                        TextInput::make('telepon')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(20)
                            ->hint('Opsional')
                            ->helperText('Nomor telepon aktif kantor kecamatan.')
                            ->validationMessages([
                                'max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
                            ]),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Email resmi kecamatan.')
                            ->validationMessages([
                                'email' => 'Format email tidak valid.',
                                'max' => 'Email tidak boleh lebih dari 255 karakter.',
                            ]),

                        TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Website resmi kecamatan jika ada.')
                            ->validationMessages([
                                'url' => 'Format URL tidak valid.',
                                'max' => 'URL tidak boleh lebih dari 255 karakter.',
                            ]),
                    ]),
            ]);
    }
}
