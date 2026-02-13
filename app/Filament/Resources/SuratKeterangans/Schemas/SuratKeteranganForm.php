<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Schemas;

use App\Enums\JenisSuratKeterangan;
use App\Filament\Resources\SuratKeterangans\Schemas\Components\AhliWarisFields;
use App\Filament\Resources\SuratKeterangans\Schemas\Components\BelumMenikahFields;
use App\Filament\Resources\SuratKeterangans\Schemas\Components\DomisiliFields;
use App\Filament\Resources\SuratKeterangans\Schemas\Components\JandaDudaFields;
use App\Filament\Resources\SuratKeterangans\Schemas\Components\KehilanganFields;
use App\Filament\Resources\SuratKeterangans\Schemas\Components\KelakuanBaikFields;
use App\Filament\Resources\SuratKeterangans\Schemas\Components\PenghasilanFields;
use App\Filament\Resources\SuratKeterangans\Schemas\Components\SudahMenikahFields;
use App\Filament\Resources\SuratKeterangans\Schemas\Components\TidakMampuFields;
use App\Filament\Resources\SuratKeterangans\Schemas\Components\UsahaFields;
use App\Helpers\DesaFieldHelper;
use App\Models\AparatDesa;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class SuratKeteranganForm
{
    protected static function isJenisSurat(mixed $value, JenisSuratKeterangan $jenis): bool
    {
        // Handle null
        if ($value === null) {
            return false;
        }

        // If it's already an enum instance
        if ($value instanceof JenisSuratKeterangan) {
            return $value === $jenis;
        }

        // If it's a string value (most common in forms)
        return (string) $value === $jenis->value;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // Step 1: Pilih Jenis Surat & Data Pemohon
                    Step::make('Jenis Surat & Pemohon')
                        ->description('Pilih jenis surat dan data pemohon')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Select::make('jenis_surat')
                                ->label('Jenis Surat Keterangan')
                                ->options(JenisSuratKeterangan::class)
                                ->required()
                                ->live()
                                ->native(false)
                                ->searchable()
                                ->helperText('Pilih jenis surat keterangan yang akan dibuat')
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
                                ->helperText(fn () => DesaFieldHelper::getDesaFieldHint())
                                ->columnSpanFull(),

                            Select::make('penduduk_id')
                                ->label('Nama Pemohon')
                                ->relationship('penduduk', 'nama_lengkap')
                                ->searchable(['nama_lengkap', 'nik'])
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if ($state) {
                                        $penduduk = \App\Models\Penduduk::find($state);
                                        if ($penduduk) {
                                            $set('nama_pemohon', $penduduk->nama_lengkap);
                                            $set('nik_pemohon', $penduduk->nik);
                                        }
                                    }
                                })
                                ->helperText('Cari berdasarkan nama atau NIK')
                                ->columnSpanFull(),

                            TextInput::make('nama_pemohon')
                                ->label('Nama Pemohon')
                                ->required()
                                ->maxLength(255)
                                ->readOnly()
                                ->hint('Otomatis')
                                ->helperText('Otomatis terisi dari data penduduk'),

                            TextInput::make('nik_pemohon')
                                ->label('NIK Pemohon')
                                ->required()
                                ->maxLength(16)
                                ->readOnly()
                                ->hint('Otomatis')
                                ->helperText('Otomatis terisi dari data penduduk'),

                            TextInput::make('keperluan')
                                ->label('Keperluan')
                                ->maxLength(255)
                                ->hint('Opsional')
                                ->helperText('Untuk apa surat ini dibuat (contoh: Melamar pekerjaan, NPWP, dll)')
                                ->columnSpanFull(),
                        ]),

                    // Step 2: Data Spesifik (Dynamic based on jenis_surat)
                    Step::make('Data Spesifik')
                        ->description('Isi data sesuai jenis surat')
                        ->icon('heroicon-o-pencil-square')
                        ->schema([
                            ...DomisiliFields::make(),
                            ...UsahaFields::make(),
                            ...TidakMampuFields::make(),
                            ...BelumMenikahFields::make(),
                            ...SudahMenikahFields::make(),
                            ...PenghasilanFields::make(),
                            ...AhliWarisFields::make(),
                            ...KehilanganFields::make(),
                            ...JandaDudaFields::make(),
                            ...KelakuanBaikFields::make(),
                        ]),

                    // Step 3: Tanda Tangan & Dokumen
                    Step::make('Dokumen & Tanda Tangan')
                        ->description('Tanda tangan pemohon dan dokumen pendukung')
                        ->icon('heroicon-o-document-arrow-up')
                        ->schema([
                            SignaturePad::make('ttd_pemohon')
                                ->label('Tanda Tangan Digital Pemohon')
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

                            FileUpload::make('foto_ttd_pemohon')
                                ->label('Foto Tanda Tangan Pemohon')
                                ->image()
                                ->imageEditor()
                                ->disk('public')
                                ->directory('surat-keterangan/tanda-tangan')
                                ->visibility('public')
                                ->maxSize(2048)
                                ->hint('Opsional')
                                ->helperText('Alternatif: Upload gambar tanda tangan (maks. 2MB).')
                                ->columnSpanFull()
                                ->validationMessages([
                                    'max' => 'Ukuran file tidak boleh lebih dari 2MB.',
                                ]),

                            FileUpload::make('dokumen_pendukung')
                                ->label('Dokumen Pendukung')
                                ->multiple()
                                ->image()
                                ->imageEditor()
                                ->disk('public')
                                ->directory('surat-keterangan/dokumen')
                                ->visibility('public')
                                ->maxSize(2048)
                                ->maxFiles(5)
                                ->hint('Opsional')
                                ->helperText('Upload dokumen pendukung (KTP, KK, dll). Maks 5 file @ 2MB')
                                ->columnSpanFull(),

                            Select::make('kepala_desa_id')
                                ->label('Kepala Desa yang Menandatangani')
                                ->options(function (Get $get) {
                                    $desaId = $get('desa_id');
                                    if (! $desaId) {
                                        return [];
                                    }

                                    return AparatDesa::where('desa_id', $desaId)
                                        ->where('jabatan', \App\Enums\JabatanAparatDesa::KEPALA_DESA)
                                        ->where('status', 'aktif')
                                        ->pluck('nama_lengkap', 'id');
                                })
                                ->searchable()
                                ->preload()
                                ->hint('Opsional')
                                ->helperText('Pilih Kepala Desa yang akan menandatangani surat')
                                ->columnSpanFull(),

                            DatePicker::make('tanggal_surat')
                                ->label('Tanggal Surat')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->default(now())
                                ->hint('Otomatis')
                                ->helperText('Tanggal pembuatan surat'),

                            Textarea::make('keterangan')
                                ->label('Keterangan Tambahan')
                                ->rows(3)
                                ->maxLength(1000)
                                ->hint('Opsional')
                                ->helperText('Catatan atau keterangan tambahan')
                                ->columnSpanFull(),
                        ]),
                ])
                    ->columnSpanFull(),
            ]);
    }
}
