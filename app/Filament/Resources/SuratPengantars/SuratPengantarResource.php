<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratPengantars;

use App\Filament\Resources\SuratPengantars\Pages\CreateSuratPengantar;
use App\Filament\Resources\SuratPengantars\Pages\EditSuratPengantar;
use App\Filament\Resources\SuratPengantars\Pages\ListSuratPengantars;
use App\Filament\Resources\SuratPengantars\Schemas\SuratPengantarForm;
use App\Filament\Resources\SuratPengantars\Tables\SuratPengantarsTable;
use App\Models\SuratPengantar;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class SuratPengantarResource extends Resource
{
    protected static ?string $model = SuratPengantar::class;

    protected static bool $isScopedToTenant = false;

    protected static string|UnitEnum|null $navigationGroup = 'Pelayanan Surat';

    protected static ?string $navigationLabel = 'Surat Pengantar';

    protected static ?string $modelLabel = 'Surat Pengantar';

    protected static ?string $pluralModelLabel = 'Surat Pengantar';

    protected static ?string $slug = 'surat-pengantar';

    protected static ?string $recordTitleAttribute = 'no_surat';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SuratPengantarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuratPengantarsTable::configure($table);
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
            'index' => ListSuratPengantars::route('/'),
            'create' => CreateSuratPengantar::route('/create'),
            'edit' => EditSuratPengantar::route('/{record}/edit'),
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
