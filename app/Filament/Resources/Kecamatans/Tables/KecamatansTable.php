<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kecamatans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class KecamatansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kecamatan')
                    ->label('Nama Kecamatan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('kode_kecamatan')
                    ->label('Kode Kecamatan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->alignment('center')
                    ->fontFamily('mono'),

                TextColumn::make('nama_kabupaten')
                    ->label('Kabupaten')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_provinsi')
                    ->label('Provinsi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('desas.nama_desa')
                    ->label('Desa')
                    ->bulleted()
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->searchable()
                    ->placeholder('Tidak ada')
                    ->state(function ($record) {
                        return $record->desas()
                            ->where('slug', '!=', 'semua-desa')
                            ->pluck('nama_desa')
                            ->toArray();
                    }),

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
