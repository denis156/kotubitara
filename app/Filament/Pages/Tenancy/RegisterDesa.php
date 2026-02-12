<?php

declare(strict_types=1);

namespace App\Filament\Pages\Tenancy;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Services\ApiWilayahService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class RegisterDesa extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Daftarkan Desa Baru';
    }

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->isSuperAdmin() || $user?->isPetugasKecamatan() ?? false;
    }

    public function form(Schema $schema): Schema
    {
        $apiWilayah = app(ApiWilayahService::class);

        return $schema
            ->columns(2)
            ->components([
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
                    ->helperText('Pilih provinsi tempat desa berada.')
                    ->columnSpanFull(),

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
                    ->helperText('Pilih kabupaten/kota sesuai provinsi di atas.')
                    ->columnSpanFull(),

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
                    ->helperText('Pilih kecamatan sesuai kabupaten/kota di atas.')
                    ->columnSpanFull(),

                Select::make('nama_desa')
                    ->label('Nama Desa')
                    ->required()
                    ->searchable()
                    ->options(function (Get $get) use ($apiWilayah) {
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

                                        // Get registered kode_desa
                                        $registeredKodeDesa = Desa::pluck('kode_desa')->toArray();

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
                    ->helperText('Pilih desa. Hanya menampilkan desa yang belum terdaftar')
                    ->columnSpanFull(),

                Textarea::make('alamat')
                    ->label('Alamat Kantor Desa')
                    ->maxLength(500)
                    ->rows(3)
                    ->columnSpanFull()
                    ->hint('Opsional')
                    ->helperText('Alamat lengkap kantor desa (Jalan, Nomor, RT/RW).'),

                TextInput::make('telepon')
                    ->label('Telepon')
                    ->tel()
                    ->maxLength(20)
                    ->hint('Opsional')
                    ->helperText('Nomor telepon aktif kantor desa/balai desa')
                    ->columnSpanFull(),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255)
                    ->hint('Opsional')
                    ->helperText('Email resmi desa untuk keperluan administrasi')
                    ->columnSpanFull(),
            ]);
    }

    protected function handleRegistration(array $data): Desa
    {
        // Step 1: Cek apakah kecamatan sudah ada, jika belum create
        $kecamatan = Kecamatan::firstOrCreate(
            ['nama_kecamatan' => $data['nama_kecamatan']],
            [
                'nama_provinsi' => $data['nama_provinsi'],
                'nama_kabupaten' => $data['nama_kabupaten'],
                'nama_kecamatan' => $data['nama_kecamatan'],
                // Kode provinsi, kabupaten, kecamatan akan auto-fill oleh KecamatanObserver
            ]
        );

        // Step 2: Create desa dengan kecamatan_id
        $desa = Desa::create([
            'kecamatan_id' => $kecamatan->id,
            'nama_desa' => $data['nama_desa'],
            'alamat' => $data['alamat'] ?? null,
            'telepon' => $data['telepon'] ?? null,
            'email' => $data['email'] ?? null,
            // slug dan kode_desa akan auto-fill oleh DesaObserver
        ]);

        // Step 3: Attach desa to user - SUDAH OTOMATIS di DesaObserver::created()
        // Tidak perlu attach manual di sini

        return $desa;
    }
}
