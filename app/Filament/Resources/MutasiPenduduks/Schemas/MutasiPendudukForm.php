<?php

declare(strict_types=1);

namespace App\Filament\Resources\MutasiPenduduks\Schemas;

use App\Enums\JenisMutasi;
use App\Helpers\DesaFieldHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MutasiPendudukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Mutasi Penduduk')
                    ->description('Informasi penduduk dan jenis mutasi.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('penduduk_id')
                            ->label('Penduduk')
                            ->relationship('penduduk', 'nama_lengkap')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama_lengkap} - {$record->nik}")
                            ->helperText('Pilih penduduk yang akan dimutasi.')
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
                            ->helperText('Desa tempat mutasi dicatat.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Desa wajib dipilih.',
                            ]),

                        Select::make('jenis_mutasi')
                            ->label('Jenis Mutasi')
                            ->options(JenisMutasi::class)
                            ->required()
                            ->native(false)
                            ->helperText('Pilih jenis mutasi penduduk.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Jenis mutasi wajib dipilih.',
                            ]),

                        DatePicker::make('tanggal_mutasi')
                            ->label('Tanggal Mutasi')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->helperText('Tanggal terjadinya mutasi.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Tanggal mutasi wajib diisi.',
                                'before_or_equal' => 'Tanggal mutasi tidak boleh di masa depan.',
                            ]),
                    ]),

                Section::make('Detail Alamat')
                    ->description('Informasi alamat asal dan tujuan.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('alamat_asal')
                            ->label('Alamat Asal')
                            ->rows(3)
                            ->maxLength(500)
                            ->hint('Opsional')
                            ->helperText('Alamat asal sebelum mutasi.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Alamat asal tidak boleh lebih dari 500 karakter.',
                            ]),

                        Textarea::make('alamat_tujuan')
                            ->label('Alamat Tujuan')
                            ->rows(3)
                            ->maxLength(500)
                            ->hint('Opsional')
                            ->helperText('Alamat tujuan setelah mutasi.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Alamat tujuan tidak boleh lebih dari 500 karakter.',
                            ]),
                    ]),

                Section::make('Dokumen & Keterangan')
                    ->description('Informasi dokumen dan alasan mutasi.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('no_surat_pindah')
                            ->label('Nomor Surat Pindah')
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Nomor surat keterangan pindah.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Nomor surat tidak boleh lebih dari 255 karakter.',
                            ]),

                        Textarea::make('alasan')
                            ->label('Alasan Mutasi')
                            ->rows(3)
                            ->maxLength(500)
                            ->hint('Opsional')
                            ->helperText('Alasan atau tujuan mutasi (cth: Pekerjaan, Pendidikan, Ikut Keluarga).')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Alasan tidak boleh lebih dari 500 karakter.',
                            ]),

                        Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->rows(3)
                            ->maxLength(500)
                            ->hint('Opsional')
                            ->helperText('Keterangan atau catatan tambahan.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Keterangan tidak boleh lebih dari 500 karakter.',
                            ]),
                    ]),
            ]);
    }
}
