<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kelahirans;

use App\Filament\Resources\Kelahirans\Pages\CreateKelahiran;
use App\Filament\Resources\Kelahirans\Pages\EditKelahiran;
use App\Filament\Resources\Kelahirans\Pages\ListKelahirans;
use App\Filament\Resources\Kelahirans\Pages\ViewKelahiran;
use App\Filament\Resources\Kelahirans\Schemas\KelahiranForm;
use App\Filament\Resources\Kelahirans\Tables\KelahiransTable;
use App\Models\Kelahiran;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class KelahiranResource extends Resource
{
    protected static ?string $model = Kelahiran::class;

    protected static bool $isScopedToTenant = false;

    protected static string | UnitEnum | null $navigationGroup = 'Data Demografi';

    protected static ?string $modelLabel = 'Kelahiran';

    protected static ?string $pluralModelLabel = 'Kelahiran';

    protected static ?string $slug = 'kelahiran';

    protected static ?string $recordTitleAttribute = 'nama_bayi';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return KelahiranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KelahiransTable::configure($table);
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
            'index' => ListKelahirans::route('/'),
            'create' => CreateKelahiran::route('/create'),
            'edit' => EditKelahiran::route('/{record}/edit'),
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
