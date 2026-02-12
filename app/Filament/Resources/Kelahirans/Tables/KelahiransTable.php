<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kelahirans\Tables;

use App\Enums\JenisKelamin;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class KelahiransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_bayi')
                    ->label('Nama Bayi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('nik_bayi')
                    ->label('NIK Bayi')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->copyable()
                    ->copyMessage('NIK disalin!')
                    ->placeholder('Belum ada'),

                TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->badge()
                    ->alignment('center'),

                TextColumn::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('waktu_lahir')
                    ->label('Waktu Lahir')
                    ->time('H:i')
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->searchable()
                    ->toggleable()
                    ->limit(20),

                TextColumn::make('ayah.nama_lengkap')
                    ->label('Nama Ayah')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('-')
                    ->getStateUsing(function ($record) {
                        return $record->ayah?->nama_lengkap ?? $record->nama_ayah ?? '-';
                    }),

                TextColumn::make('ibu.nama_lengkap')
                    ->label('Nama Ibu')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('-')
                    ->getStateUsing(function ($record) {
                        return $record->ibu?->nama_lengkap ?? $record->nama_ibu ?? '-';
                    }),

                TextColumn::make('berat_lahir')
                    ->label('Berat (kg)')
                    ->numeric(decimalPlaces: 2)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                TextColumn::make('panjang_lahir')
                    ->label('Panjang (cm)')
                    ->numeric(decimalPlaces: 2)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                TextColumn::make('no_surat_kelahiran')
                    ->label('No. Surat')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->copyable()
                    ->copyMessage('Nomor surat disalin!')
                    ->toggleable()
                    ->placeholder('Belum ada'),

                TextColumn::make('desa.nama_desa')
                    ->label('Desa')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(JenisKelamin::class)
                    ->native(false)
                    ->indicator('Jenis Kelamin'),

                SelectFilter::make('desa')
                    ->label('Desa')
                    ->relationship('desa', 'nama_desa')
                    ->searchable()
                    ->preload()
                    ->indicator('Desa'),

                TrashedFilter::make()
                    ->label('Status')
                    ->native(false),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn ($action) => $action
                    ->button()
                    ->size(Size::Medium)
                    ->label('Filter')
            )
            ->columnManagerTriggerAction(
                fn ($action) => $action
                    ->button()
                    ->size(Size::Medium)
                    ->label('Kolom')
            )
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->icon('heroicon-o-pencil')
                    ->color('info')
                    ->button(),
                DeleteAction::make()
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->button()
                    ->outlined(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_lahir', 'desc');
    }
}
