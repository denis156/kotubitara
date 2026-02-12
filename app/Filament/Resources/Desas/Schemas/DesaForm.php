<?php

declare(strict_types=1);

namespace App\Filament\Resources\Desas\Schemas;

use App\Models\Desa;
use Filament\Schemas\Schema;
use App\Services\ApiWilayahService;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class DesaForm
{
    public static function configure(Schema $schema, ?Model $record = null): Schema
    {
        $apiWilayah = app(ApiWilayahService::class);

        return $schema
            ->components([
                Section::make('Informasi Wilayah')
                    ->description('Pilih wilayah administrasi secara berurutan mulai dari Provinsi hingga Desa.')
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
                                $set('nama_desa', null);
                            })
                            ->live()
                            ->disabled(fn () => Auth::user()?->isPetugasDesa())
                            ->helperText('Pilih provinsi tempat desa berada.')
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
                                $set('nama_desa', null);
                            })
                            ->live()
                            ->disabled(fn () => Auth::user()?->isPetugasDesa())
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
                            ->afterStateUpdated(fn (callable $set) => $set('nama_desa', null))
                            ->live()
                            ->disabled(fn () => Auth::user()?->isPetugasDesa())
                            ->helperText('Pilih kecamatan sesuai kabupaten/kota di atas.')
                            ->validationMessages([
                                'required' => 'Kecamatan wajib dipilih.',
                            ]),

                        Select::make('nama_desa')
                            ->label('Nama Desa')
                            ->required()
                            ->searchable()
                            ->options(function (Get $get) use ($apiWilayah, $record) {
                                $namaProvinsi = $get('nama_provinsi');
                                $namaKabupaten = $get('nama_kabupaten');
                                $namaKecamatan = $get('nama_kecamatan');

                                if ($namaProvinsi && $namaKabupaten && $namaKecamatan) {
                                    $provinces = $apiWilayah->getProvinces();
                                    $province = $provinces->firstWhere('name', $namaProvinsi);

                                    if ($province) {
                                        $regencies = $apiWilayah->getRegencies($province['id']);
                                        $regency = $regencies->firstWhere('name', $namaKabupaten);

                                        if ($regency) {
                                            $districts = $apiWilayah->getDistricts($regency['id']);
                                            $district = $districts->firstWhere('name', $namaKecamatan);

                                            if ($district) {
                                                $villages = $apiWilayah->getVillages($district['id']);

                                                // Get registered kode_desa, exclude current record if editing
                                                $registeredKodeDesa = Desa::when($record, function ($query) use ($record) {
                                                    return $query->where('id', '!=', $record->id);
                                                })->pluck('kode_desa')->toArray();

                                                // Filter out registered villages
                                                $availableVillages = $villages->filter(function ($village) use ($registeredKodeDesa) {
                                                    return ! in_array($village['id'], $registeredKodeDesa);
                                                });

                                                return $availableVillages->pluck('name', 'name')->toArray();
                                            }
                                        }
                                    }
                                }
                                return [];
                            })
                            ->disabled(fn () => Auth::user()?->isPetugasDesa())
                            ->helperText('Pilih desa. Hanya menampilkan desa yang belum terdaftar.')
                            ->validationMessages([
                                'required' => 'Nama desa wajib dipilih.',
                            ]),
                    ]),

                Section::make('Informasi Kontak')
                    ->description('Lengkapi data kontak resmi kantor desa untuk keperluan korespondensi.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('alamat')
                            ->label('Alamat Kantor Desa')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull()
                            ->hint('Opsional')
                            ->helperText('Alamat lengkap kantor desa (Jalan, Nomor, RT/RW).')
                            ->validationMessages([
                                'max' => 'Alamat tidak boleh lebih dari 500 karakter.',
                            ]),

                        TextInput::make('telepon')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(20)
                            ->hint('Opsional')
                            ->helperText('Nomor telepon aktif kantor desa/balai desa.')
                            ->validationMessages([
                                'max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
                            ]),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Email resmi desa untuk keperluan administrasi.')
                            ->validationMessages([
                                'email' => 'Format email tidak valid.',
                                'max' => 'Email tidak boleh lebih dari 255 karakter.',
                            ]),
                    ]),
            ]);
    }
}
