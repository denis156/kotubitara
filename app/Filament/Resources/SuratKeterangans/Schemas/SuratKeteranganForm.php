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
use App\Filament\Resources\SuratKeterangans\Schemas\Components\KematianFields;
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
                    // Step 1: Pilih Jenis Surat & Data Pemohon/Pelapor
                    Step::make('Jenis Surat & Pemohon/Pelapor')
                        ->description('Pilih jenis surat dan data pemohon/pelapor')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            TextInput::make('no_surat')
                                ->label('Nomor Surat')
                                ->readOnly()
                                ->hint('Otomatis')
                                ->helperText('Nomor surat akan digenerate otomatis oleh sistem')
                                ->columnSpanFull(),

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
                                ->live()
                                ->default(fn () => DesaFieldHelper::getDefaultDesaId())
                                ->disabled(fn () => DesaFieldHelper::shouldDisableDesaField())
                                ->dehydrated()
                                ->helperText(fn () => DesaFieldHelper::getDesaFieldHint())
                                ->columnSpanFull(),

                            Select::make('penduduk_id')
                                ->label('Pilih Pemohon/Pelapor (Penduduk Terdaftar)')
                                ->relationship(
                                    name: 'penduduk',
                                    titleAttribute: 'nama_lengkap',
                                    modifyQueryUsing: fn ($query, Get $get) => $query->when(
                                        $get('desa_id'),
                                        fn ($q, $desaId) => $q->where('desa_id', $desaId)
                                    )
                                )
                                ->searchable(['nama_lengkap', 'nik'])
                                ->preload()
                                ->live()
                                ->hint('Opsional')
                                ->helperText('Cari berdasarkan nama atau NIK. Hanya menampilkan penduduk dari desa yang dipilih.')
                                ->visible(fn (Get $get) => static::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KEMATIAN) === false)
                                ->columnSpanFull(),

                            TextInput::make('data_tambahan.nama_pemohon_manual')
                                ->label('Nama Pemohon/Pelapor')
                                ->visible(fn (Get $get) => static::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KEMATIAN) === false && ! $get('penduduk_id'))
                                ->required(fn (Get $get) => static::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KEMATIAN) === false && ! $get('penduduk_id'))
                                ->maxLength(255)
                                ->helperText('Isi manual jika pemohon/pelapor bukan penduduk terdaftar')
                                ->columnSpanFull(),

                            TextInput::make('data_tambahan.nik_pemohon_manual')
                                ->label('NIK Pemohon/Pelapor')
                                ->visible(fn (Get $get) => static::isJenisSurat($get('jenis_surat'), JenisSuratKeterangan::KEMATIAN) === false && ! $get('penduduk_id'))
                                ->hint('Opsional')
                                ->maxLength(16)
                                ->length(16)
                                ->helperText('NIK pemohon/pelapor (16 digit)')
                                ->columnSpanFull(),

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
                            ...KematianFields::make(),
                        ]),

                    // Step 3: Tanda Tangan
                    Step::make('Tanda Tangan')
                        ->description('Tanda tangan pemohon/pelapor')
                        ->icon('heroicon-o-pencil')
                        ->schema([
                            SignaturePad::make('data_tambahan.ttd_pemohon')
                                ->label('Tanda Tangan Digital Pemohon/Pelapor')
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

                            FileUpload::make('data_tambahan.foto_ttd_pemohon')
                                ->label('Foto Tanda Tangan Pemohon/Pelapor')
                                ->image()
                                ->imageEditor()
                                ->disk('public')
                                ->directory('surat-keterangan/tanda-tangan')
                                ->visibility('public')
                                ->maxSize(2048)
                                ->hint('Opsional')
                                ->helperText('Alternatif: Upload gambar tanda tangan pemohon/pelapor (maks. 2MB).')
                                ->columnSpanFull()
                                ->validationMessages([
                                    'max' => 'Ukuran file tidak boleh lebih dari 2MB.',
                                ]),

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
