<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Foto')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=FFFFFF&background=000000'),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email disalin!')
                    ->fontFamily('mono'),

                TextColumn::make('telepon')
                    ->label('Telepon')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Tidak ada')
                    ->toggleable()
                    ->fontFamily('mono'),

                TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->alignment('center'),

                TextColumn::make('desas.nama_desa')
                    ->label('Desa')
                    ->bulleted()
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->searchable()
                    ->placeholder('Tidak ada'),

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
                SelectFilter::make('role')
                    ->label('Peran Pengguna')
                    ->options(function () {
                        $user = Auth::user();

                        // Super Admin bisa filter semua role
                        if ($user?->isSuperAdmin()) {
                            return UserRole::class;
                        }

                        // Petugas Kecamatan hanya bisa filter Petugas Kecamatan & Petugas Desa
                        return [
                            UserRole::PETUGAS_KECAMATAN->value => UserRole::PETUGAS_KECAMATAN->getLabel(),
                            UserRole::PETUGAS_DESA->value => UserRole::PETUGAS_DESA->getLabel(),
                        ];
                    })
                    ->native(false)
                    ->indicator('Peran'),

                SelectFilter::make('desa')
                    ->label('Desa')
                    ->relationship('desas', 'nama_desa')
                    ->multiple()
                    ->preload()
                    ->searchable()
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
