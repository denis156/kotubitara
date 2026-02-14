<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans\Tables;

use App\Enums\JenisSuratKeterangan;
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
use Illuminate\Support\Str;

class SuratKeterangansTable
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

                TextColumn::make('desa.nama_desa')
                    ->label('Desa')
                    ->formatStateUsing(fn (string $state): string => Str::title(strtolower($state)))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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

                TextColumn::make('status_ttd')
                    ->label('Status TTD')
                    ->state(function ($record) {
                        $hasTTD = isset($record->data_tambahan['ttd_pemohon']) || isset($record->data_tambahan['foto_ttd_pemohon']);

                        return $hasTTD ? 'Sudah TTD' : 'Belum TTD';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sudah TTD' => 'success',
                        'Belum TTD' => 'warning',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Sudah TTD' => 'heroicon-o-check-circle',
                        'Belum TTD' => 'heroicon-o-exclamation-circle',
                    })
                    ->alignment('center')
                    ->toggleable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->keterangan)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-')
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->description(fn ($record) => $record->created_at->diffForHumans()),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis_surat')
                    ->label('Jenis Surat')
                    ->options(JenisSuratKeterangan::class)
                    ->native(false)
                    ->multiple()
                    ->indicator('Jenis Surat'),

                SelectFilter::make('status_ttd')
                    ->label('Status TTD')
                    ->options([
                        'sudah' => 'Sudah TTD',
                        'belum' => 'Belum TTD',
                    ])
                    ->native(false)
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'sudah') {
                            return $query->where(function ($q) {
                                $q->whereNotNull('data_tambahan->ttd_pemohon')
                                    ->orWhereNotNull('data_tambahan->foto_ttd_pemohon');
                            });
                        }
                        if ($data['value'] === 'belum') {
                            return $query->where(function ($q) {
                                $q->whereNull('data_tambahan->ttd_pemohon')
                                    ->whereNull('data_tambahan->foto_ttd_pemohon');
                            });
                        }

                        return $query;
                    })
                    ->indicator('Status TTD'),

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
