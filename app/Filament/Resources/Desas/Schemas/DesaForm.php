<?php

declare(strict_types=1);

namespace App\Filament\Resources\Desas\Schemas;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Services\ApiWilayahService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DesaForm
{
    public static function configure(Schema $schema, ?Model $record = null): Schema
    {
        $apiWilayah = app(ApiWilayahService::class);

        return $schema
            ->components([
                Section::make('Informasi Wilayah')
                    ->description('Pilih kecamatan dan desa untuk mendaftarkan desa baru.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('kecamatan_id')
                            ->label('Kecamatan')
                            ->required()
                            ->searchable()
                            ->relationship('kecamatan', 'nama_kecamatan')
                            ->preload()
                            ->afterStateUpdated(fn (callable $set) => $set('nama_desa', null))
                            ->live()
                            ->disabled(fn () => ! Auth::user()?->isSuperAdmin())
                            ->dehydrated(fn () => Auth::user()?->isSuperAdmin())
                            ->hint(fn () => ! Auth::user()?->isSuperAdmin() ? 'Otomatis' : null)
                            ->default(function () {
                                $user = Auth::user();

                                // Jika bukan Super Admin, ambil kecamatan dari desa yang dikelola
                                if (! $user?->isSuperAdmin()) {
                                    $firstDesa = $user->desas()->where('slug', '!=', 'semua-desa')->first();

                                    return $firstDesa?->kecamatan_id;
                                }

                                return null;
                            })
                            ->helperText('Pilih kecamatan tempat desa berada.')
                            ->validationMessages([
                                'required' => 'Kecamatan wajib dipilih.',
                            ]),

                        Select::make('nama_desa')
                            ->label('Nama Desa')
                            ->required()
                            ->searchable()
                            ->options(function (Get $get) use ($apiWilayah, $record) {
                                $kecamatanId = $get('kecamatan_id');

                                if ($kecamatanId) {
                                    $kecamatan = Kecamatan::find($kecamatanId);

                                    if ($kecamatan) {
                                        $villages = $apiWilayah->getVillages($kecamatan->kode_kecamatan);

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
