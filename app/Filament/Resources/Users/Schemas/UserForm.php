<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengguna')
                    ->description('Lengkapi identitas dan kredensial pengguna.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->label('Foto Profil')
                            ->avatar()
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->hint('Opsional')
                            ->helperText('Upload foto profil (Maks. 2MB, format: JPG, PNG).')
                            ->columnSpanFull()
                            ->validationMessages([
                                'image' => 'File harus berupa gambar.',
                                'max' => 'Ukuran file tidak boleh lebih dari 2MB.',
                            ]),

                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->autocomplete('name')
                            ->helperText('Nama lengkap sesuai KTP/Identitas resmi.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Nama lengkap wajib diisi.',
                                'max' => 'Nama tidak boleh lebih dari 255 karakter.',
                            ]),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->autocomplete('email')
                            ->helperText('Email aktif untuk login dan notifikasi.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Email wajib diisi.',
                                'email' => 'Format email tidak valid.',
                                'max' => 'Email tidak boleh lebih dari 255 karakter.',
                                'unique' => 'Email sudah terdaftar. Gunakan email lain.',
                            ]),

                        TextInput::make('telepon')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(20)
                            ->hint('Opsional')
                            ->helperText('Nomor telepon aktif yang dapat dihubungi.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
                            ]),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->maxLength(255)
                            ->autocomplete('new-password')
                            ->hint(fn (string $operation): ?string => $operation === 'edit' ? 'Opsional' : null)
                            ->helperText(fn (string $operation): string => $operation === 'edit'
                                ? 'Kosongkan jika tidak ingin mengubah password.'
                                : 'Password minimal 8 karakter.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Password wajib diisi.',
                                'max' => 'Password tidak boleh lebih dari 255 karakter.',
                            ]),

                        Select::make('role')
                            ->label('Peran Pengguna')
                            ->options(function () {
                                $user = Auth::user();

                                // Super Admin bisa pilih semua role
                                if ($user?->isSuperAdmin()) {
                                    return UserRole::class;
                                }

                                // Petugas Kecamatan hanya bisa pilih Petugas Kecamatan & Petugas Desa
                                // TIDAK BISA pilih Super Admin
                                return [
                                    UserRole::PETUGAS_KECAMATAN->value => UserRole::PETUGAS_KECAMATAN->getLabel(),
                                    UserRole::PETUGAS_DESA->value => UserRole::PETUGAS_DESA->getLabel(),
                                ];
                            })
                            ->required()
                            ->native(false)
                            ->helperText('Pilih peran pengguna sesuai tingkat akses.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Peran pengguna wajib dipilih.',
                            ]),
                    ]),

                Section::make('Akses Desa')
                    ->description('Tentukan hak akses wilayah desa pengguna.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('desas')
                            ->label('Desa yang Dapat Diakses')
                            ->relationship(titleAttribute: 'nama_desa')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('Pilih desa yang dapat diakses oleh pengguna ini.')
                            ->validationMessages([
                                'required' => 'Minimal satu desa harus dipilih.',
                            ]),
                    ]),
            ]);
    }
}
