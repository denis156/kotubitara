<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kematians\Tables;

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

class KematiansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('penduduk.nama_lengkap')
                    ->label('Nama Penduduk')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('penduduk.nik')
                    ->label('NIK')
                    ->searchable()
                    ->fontFamily('mono')
                    ->toggleable(),

                TextColumn::make('tanggal_meninggal')
                    ->label('Tanggal Meninggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('tempat_meninggal')
                    ->label('Tempat Meninggal')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('sebab_kematian')
                    ->label('Sebab Kematian')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('no_surat_kematian')
                    ->label('No. Surat Kematian')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->copyable()
                    ->copyMessage('Nomor surat kematian disalin!')
                    ->placeholder('Belum ada')
                    ->toggleable(),

                TextColumn::make('desa.nama_desa')
                    ->label('Desa')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->placeholder('Tidak ada')
                    ->toggleable(isToggledHiddenByDefault: true),

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
            ->defaultSort('created_at', 'desc');
    }
}
