<?php

declare(strict_types=1);

namespace App\Filament\Resources\KartuKeluargas\Schemas;

use App\Enums\HubunganKeluarga;
use App\Helpers\FilamentHelper;
use App\Models\Penduduk;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class KartuKeluargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Kartu Keluarga')
                    ->description('Data identitas dan kepala keluarga.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('no_kk')
                            ->label('Nomor Kartu Keluarga (KK)')
                            ->required()
                            ->length(16)
                            ->unique(ignoreRecord: true)
                            ->helperText('Nomor KK harus 16 digit sesuai KK.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Nomor KK wajib diisi.',
                                'digits' => 'Nomor KK harus tepat 16 digit.',
                                'unique' => 'Nomor KK sudah terdaftar. Gunakan nomor KK lain.',
                            ]),

                        Select::make('desa_id')
                            ->label('Desa')
                            ->relationship('desa', 'nama_desa')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(fn () => FilamentHelper::getDefaultDesaId())
                            ->disabled(fn () => FilamentHelper::shouldDisableDesaField())
                            ->dehydrated()
                            ->live()
                            ->afterStateUpdated(function (callable $set) {
                                $set('kepala_keluarga_id', null);
                                $set('anggota_keluarga', null);
                            })
                            ->helperText('Pilih desa tempat KK terdaftar.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Desa wajib dipilih.',
                            ]),

                        Select::make('kepala_keluarga_id')
                            ->label('Kepala Keluarga')
                            ->options(function (Get $get, $record) {
                                $desaId = $get('desa_id');

                                if (! $desaId) {
                                    return [];
                                }

                                return Penduduk::where('desa_id', $desaId)
                                    ->where('hubungan_keluarga', HubunganKeluarga::KEPALA_KELUARGA)
                                    ->where(function ($query) use ($record) {
                                        $query->whereNull('kartu_keluarga_id')
                                            ->when($record?->id, fn ($q) => $q->orWhere('kartu_keluarga_id', $record->id))
                                            ->when($record?->kepala_keluarga_id, fn ($q) => $q->orWhere('id', $record->kepala_keluarga_id));
                                    })
                                    ->get()
                                    ->mapWithKeys(fn ($penduduk) => [
                                        $penduduk->id => "{$penduduk->nama_lengkap} - {$penduduk->nik}",
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->helperText('Hanya menampilkan penduduk dengan hubungan keluarga "Kepala Keluarga".')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Kepala keluarga wajib dipilih.',
                            ]),

                        Select::make('anggota_keluarga')
                            ->label('Anggota Keluarga Lainnya')
                            ->multiple()
                            ->options(function (Get $get, $record) {
                                $desaId = $get('desa_id');
                                $kepalaKeluargaId = $get('kepala_keluarga_id');

                                if (! $desaId) {
                                    return [];
                                }

                                return Penduduk::where('desa_id', $desaId)
                                    // Exclude yang hubungan keluarga-nya "Kepala Keluarga"
                                    ->where('hubungan_keluarga', '!=', HubunganKeluarga::KEPALA_KELUARGA)
                                    ->where(function ($query) use ($record) {
                                        $query->whereNull('kartu_keluarga_id')
                                            ->when($record?->id, fn ($q) => $q->orWhere('kartu_keluarga_id', $record->id));
                                    })
                                    // Exclude kepala keluarga yang dipilih dari list anggota
                                    ->when($kepalaKeluargaId, fn ($q) => $q->where('id', '!=', $kepalaKeluargaId))
                                    ->get()
                                    ->mapWithKeys(fn ($penduduk) => [
                                        $penduduk->id => "{$penduduk->nama_lengkap} - {$penduduk->nik} ({$penduduk->hubungan_keluarga->getLabel()})",
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->hint('Opsional')
                            ->helperText('Pilih anggota keluarga selain kepala keluarga.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Alamat Keluarga')
                    ->description('Alamat lengkap tempat tinggal keluarga.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->maxLength(500)
                            ->hint('Opsional')
                            ->helperText('Alamat lengkap sesuai KK (Jalan, Nomor Rumah, Dusun).')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Alamat tidak boleh lebih dari 500 karakter.',
                            ]),

                        TextInput::make('rt')
                            ->label('RT')
                            ->maxLength(10)
                            ->hint('Opsional')
                            ->helperText('Nomor RT (Rukun Tetangga).')
                            ->validationMessages([
                                'max' => 'RT tidak boleh lebih dari 10 karakter.',
                            ]),

                        TextInput::make('rw')
                            ->label('RW')
                            ->maxLength(10)
                            ->hint('Opsional')
                            ->helperText('Nomor RW (Rukun Warga).')
                            ->validationMessages([
                                'max' => 'RW tidak boleh lebih dari 10 karakter.',
                            ]),
                    ]),
            ]);
    }
}
