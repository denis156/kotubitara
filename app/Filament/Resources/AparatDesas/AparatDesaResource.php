<?php

declare(strict_types=1);

namespace App\Filament\Resources\AparatDesas;

use App\Filament\Resources\AparatDesas\Pages\CreateAparatDesa;
use App\Filament\Resources\AparatDesas\Pages\EditAparatDesa;
use App\Filament\Resources\AparatDesas\Pages\ListAparatDesas;
use App\Filament\Resources\AparatDesas\Schemas\AparatDesaForm;
use App\Filament\Resources\AparatDesas\Tables\AparatDesasTable;
use App\Models\AparatDesa;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class AparatDesaResource extends Resource
{
    protected static ?string $model = AparatDesa::class;

    protected static bool $isScopedToTenant = false;

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $modelLabel = 'Aparat Desa';

    protected static ?string $pluralModelLabel = 'Aparat Desa';

    protected static ?string $slug = 'aparat-desa';

    protected static ?string $recordTitleAttribute = 'nama_lengkap';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return AparatDesaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AparatDesasTable::configure($table);
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
            'index' => ListAparatDesas::route('/'),
            'create' => CreateAparatDesa::route('/create'),
            'edit' => EditAparatDesa::route('/{record}/edit'),
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
