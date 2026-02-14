<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeterangans;

use App\Filament\Resources\SuratKeterangans\Pages\CreateSuratKeterangan;
use App\Filament\Resources\SuratKeterangans\Pages\EditSuratKeterangan;
use App\Filament\Resources\SuratKeterangans\Pages\ListSuratKeterangans;
use App\Filament\Resources\SuratKeterangans\Pages\ViewSuratKeterangan;
use App\Filament\Resources\SuratKeterangans\Schemas\SuratKeteranganForm;
use App\Filament\Resources\SuratKeterangans\Tables\SuratKeterangansTable;
use App\Models\SuratKeterangan;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class SuratKeteranganResource extends Resource
{
    protected static ?string $model = SuratKeterangan::class;

    protected static bool $isScopedToTenant = false;

    protected static string|UnitEnum|null $navigationGroup = 'Pelayanan Surat';

    protected static ?string $navigationLabel = 'Surat Keterangan';

    protected static ?string $modelLabel = 'Surat Keterangan';

    protected static ?string $pluralModelLabel = 'Surat Keterangan';

    protected static ?string $slug = 'surat-keterangan';

    protected static ?string $recordTitleAttribute = 'no_surat';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SuratKeteranganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuratKeterangansTable::configure($table);
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
            'index' => ListSuratKeterangans::route('/'),
            'create' => CreateSuratKeterangan::route('/create'),
            'view' => ViewSuratKeterangan::route('/{record}'),
            'edit' => EditSuratKeterangan::route('/{record}/edit'),
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
