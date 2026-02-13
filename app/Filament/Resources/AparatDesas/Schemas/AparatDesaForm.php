<?php

declare(strict_types=1);

namespace App\Filament\Resources\AparatDesas\Schemas;

use App\Enums\JabatanAparatDesa;
use App\Helpers\DesaFieldHelper;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class AparatDesaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas & Jabatan')
                    ->description('Data identitas dan jabatan aparat desa.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('desa_id')
                            ->label('Desa')
                            ->relationship('desa', 'nama_desa')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(fn () => DesaFieldHelper::getDefaultDesaId())
                            ->disabled(fn () => DesaFieldHelper::shouldDisableDesaField())
                            ->dehydrated()
                            ->hint(fn () => DesaFieldHelper::getDesaFieldHint())
                            ->helperText('Desa tempat aparat bertugas.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Desa wajib dipilih.',
                            ]),

                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nama lengkap aparat desa.')
                            ->validationMessages([
                                'required' => 'Nama lengkap wajib diisi.',
                                'max' => 'Nama lengkap tidak boleh lebih dari 255 karakter.',
                            ]),

                        TextInput::make('nip')
                            ->label('NIP')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->hint('Opsional')
                            ->helperText('Nomor Induk Pegawai (jika ada).')
                            ->validationMessages([
                                'max' => 'NIP tidak boleh lebih dari 255 karakter.',
                                'unique' => 'NIP sudah terdaftar.',
                            ]),

                        Select::make('jabatan')
                            ->label('Jabatan')
                            ->options(JabatanAparatDesa::class)
                            ->required()
                            ->native(false)
                            ->live()
                            ->helperText('Jabatan aparat desa.')
                            ->validationMessages([
                                'required' => 'Jabatan wajib dipilih.',
                            ]),

                        TextInput::make('nama_dusun')
                            ->label('Nama Dusun')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => $get('jabatan') === JabatanAparatDesa::KEPALA_DUSUN)
                            ->required(fn (Get $get): bool => $get('jabatan') === JabatanAparatDesa::KEPALA_DUSUN)
                            ->helperText('Nama dusun untuk Kepala Dusun.')
                            ->validationMessages([
                                'required' => 'Nama dusun wajib diisi untuk Kepala Dusun.',
                                'max' => 'Nama dusun tidak boleh lebih dari 255 karakter.',
                            ]),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'aktif' => 'Aktif',
                                'non-aktif' => 'Non-Aktif',
                            ])
                            ->required()
                            ->default('aktif')
                            ->native(false)
                            ->helperText('Status keaktifan aparat.')
                            ->validationMessages([
                                'required' => 'Status wajib dipilih.',
                            ]),
                    ]),

                Section::make('Kontak & Periode Jabatan')
                    ->description('Informasi kontak dan periode masa jabatan.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('telepon')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Nomor telepon aparat desa.')
                            ->validationMessages([
                                'max' => 'Nomor telepon tidak boleh lebih dari 255 karakter.',
                            ]),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->hint('Opsional')
                            ->helperText('Alamat email aparat desa.')
                            ->validationMessages([
                                'email' => 'Format email tidak valid.',
                                'max' => 'Email tidak boleh lebih dari 255 karakter.',
                            ]),

                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->maxLength(1000)
                            ->rows(3)
                            ->hint('Opsional')
                            ->helperText('Alamat tempat tinggal aparat desa.')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Alamat tidak boleh lebih dari 1000 karakter.',
                            ]),

                        DatePicker::make('tanggal_mulai_jabatan')
                            ->label('Tanggal Mulai Jabatan')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->hint('Opsional')
                            ->helperText('Tanggal mulai menjabat.')
                            ->validationMessages([
                                'date' => 'Format tanggal tidak valid.',
                            ]),

                        DatePicker::make('tanggal_selesai_jabatan')
                            ->label('Tanggal Selesai Jabatan')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->hint('Opsional')
                            ->helperText('Tanggal selesai menjabat (jika ada).')
                            ->validationMessages([
                                'date' => 'Format tanggal tidak valid.',
                            ]),
                    ]),

                Section::make('Foto & Tanda Tangan')
                    ->description('Foto dan tanda tangan untuk dokumen resmi.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('foto')
                            ->label('Foto Aparat Desa')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('aparat-desa/foto')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->hint('Opsional')
                            ->helperText('Upload foto aparat desa (maks. 2MB).')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Ukuran file tidak boleh lebih dari 2MB.',
                            ]),

                        SignaturePad::make('ttd_digital')
                            ->label('Tanda Tangan Digital')
                            ->backgroundColor('rgba(250, 250, 250, 1)')
                            ->backgroundColorOnDark('rgba(30, 30, 30, 1)')
                            ->exportBackgroundColor('rgb(255, 255, 255)')
                            ->penColor('#000')
                            ->penColorOnDark('#000')
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
                            ->hint('Opsional')
                            ->helperText('Tanda tangan di area kotak dengan mouse atau touchscreen.')
                            ->columnSpanFull()
                            ->clearAction(fn (Action $action) => $action->button())
                            ->undoAction(fn (Action $action) => $action->button()->icon('heroicon-o-arrow-uturn-left'))
                            ->doneAction(fn (Action $action) => $action->button()->icon('heroicon-o-check-circle')),

                        FileUpload::make('foto_ttd')
                            ->label('Upload Foto Tanda Tangan')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('aparat-desa/tanda-tangan')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->hint('Opsional')
                            ->helperText('Alternatif: Upload gambar tanda tangan (maks. 2MB).')
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Ukuran file tidak boleh lebih dari 2MB.',
                            ]),
                    ]),
            ]);
    }
}
