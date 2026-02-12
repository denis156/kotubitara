<?php

declare(strict_types=1);

namespace App\Filament\Resources\MutasiPenduduks;

use App\Filament\Resources\MutasiPenduduks\Pages\CreateMutasiPenduduk;
use App\Filament\Resources\MutasiPenduduks\Pages\EditMutasiPenduduk;
use App\Filament\Resources\MutasiPenduduks\Pages\ListMutasiPenduduks;
use App\Filament\Resources\MutasiPenduduks\Schemas\MutasiPendudukForm;
use App\Filament\Resources\MutasiPenduduks\Tables\MutasiPenduduksTable;
use App\Models\MutasiPenduduk;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class MutasiPendudukResource extends Resource
{
    protected static ?string $model = MutasiPenduduk::class;

    protected static bool $isScopedToTenant = false;

    protected static string | UnitEnum | null $navigationGroup = 'Data Demografi';

    protected static ?string $modelLabel = 'Mutasi Penduduk';

    protected static ?string $pluralModelLabel = 'Mutasi Penduduk';

    protected static ?string $slug = 'mutasi-penduduk';

    protected static ?string $recordTitleAttribute = 'no_surat_pindah';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return MutasiPendudukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MutasiPenduduksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMutasiPenduduks::route('/'),
            'create' => CreateMutasiPenduduk::route('/create'),
            'edit' => EditMutasiPenduduk::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        $tenant = \Filament\Facades\Filament::getTenant();

        if ($tenant && $tenant->slug !== 'semua-desa') {
            $query->where('desa_id', $tenant->id);
        }

        return $query;
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
