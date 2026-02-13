<?php

declare(strict_types=1);

namespace App\Filament\Resources\Kematians;

use App\Filament\Resources\Kematians\Pages\CreateKematian;
use App\Filament\Resources\Kematians\Pages\EditKematian;
use App\Filament\Resources\Kematians\Pages\ListKematians;
use App\Filament\Resources\Kematians\Pages\ViewKematian;
use App\Filament\Resources\Kematians\Schemas\KematianForm;
use App\Filament\Resources\Kematians\Tables\KematiansTable;
use App\Models\Kematian;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class KematianResource extends Resource
{
    protected static ?string $model = Kematian::class;

    protected static bool $isScopedToTenant = false;

    protected static string|UnitEnum|null $navigationGroup = 'Data Demografi';

    protected static ?string $modelLabel = 'Kematian';

    protected static ?string $pluralModelLabel = 'Kematian';

    protected static ?string $slug = 'kematian';

    protected static ?string $recordTitleAttribute = 'no_surat_kematian';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return KematianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KematiansTable::configure($table);
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
            'index' => ListKematians::route('/'),
            'create' => CreateKematian::route('/create'),
            'edit' => EditKematian::route('/{record}/edit'),
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
