<?php

declare(strict_types=1);

namespace App\Filament\Resources\Desas;

use App\Filament\Resources\Desas\Pages\CreateDesa;
use App\Filament\Resources\Desas\Pages\EditDesa;
use App\Filament\Resources\Desas\Pages\ListDesas;
use App\Filament\Resources\Desas\Schemas\DesaForm;
use App\Filament\Resources\Desas\Tables\DesasTable;
use App\Models\Desa;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class DesaResource extends Resource
{
    protected static ?string $model = Desa::class;

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $modelLabel = 'Desa';

    protected static ?string $pluralModelLabel = 'Desa';

    protected static ?string $slug = 'desa';

    protected static ?string $recordTitleAttribute = 'nama_desa';

    protected static ?int $navigationSort = 4;

    protected static bool $isScopedToTenant = false;

    public static function form(Schema $schema): Schema
    {
        return DesaForm::configure($schema, $schema->getRecord());
    }

    public static function table(Table $table): Table
    {
        return DesasTable::configure($table);
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
            'index' => ListDesas::route('/'),
            'create' => CreateDesa::route('/create'),
            'edit' => EditDesa::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
