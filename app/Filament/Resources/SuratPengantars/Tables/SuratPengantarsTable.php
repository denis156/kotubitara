<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars\Tables;

use App\Enums\JenisSuratPengantar;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SuratPengantarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_surat')
                    ->label('No. Surat')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->copyable()
                    ->copyMessage('Nomor surat disalin!')
                    ->placeholder('Belum ada')
                    ->weight('bold'),

                TextColumn::make('jenis_surat')
                    ->label('Jenis Surat')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->alignment('center'),

                TextColumn::make('nama_pemohon')
                    ->label('Nama Pemohon')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('nik_pemohon')
                    ->label('NIK Pemohon')
                    ->searchable()
                    ->fontFamily('mono')
                    ->copyable()
                    ->copyMessage('NIK disalin!')
                    ->toggleable(),

                TextColumn::make('ditujukan_kepada')
                    ->label('Ditujukan Kepada')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('keperluan')
                    ->label('Keperluan')
                    ->searchable()
                    ->limit(30)
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('desa.nama_desa')
                    ->label('Desa')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('kepalaDesa.nama_lengkap')
                    ->label('Kepala Desa')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('tanggal_surat')
                    ->label('Tanggal Surat')
                    ->date('d M Y')
                    ->sortable()
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
                SelectFilter::make('jenis_surat')
                    ->label('Jenis Surat')
                    ->options(JenisSuratPengantar::class)
                    ->native(false)
                    ->indicator('Jenis Surat'),

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
                fn (Action $action) => $action
                    ->button()
                    ->size(Size::Medium)
                    ->label('Filter')
            )
            ->columnManagerTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->size(Size::Medium)
                    ->label('Kolom')
            )
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->button(),
                EditAction::make()
                    ->label('Ubah')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
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
