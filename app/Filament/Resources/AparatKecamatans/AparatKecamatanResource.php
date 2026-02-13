<?php

declare(strict_types=1);

namespace App\Filament\Resources\AparatKecamatans;

use App\Filament\Resources\AparatKecamatans\Pages\CreateAparatKecamatan;
use App\Filament\Resources\AparatKecamatans\Pages\EditAparatKecamatan;
use App\Filament\Resources\AparatKecamatans\Pages\ListAparatKecamatans;
use App\Filament\Resources\AparatKecamatans\Schemas\AparatKecamatanForm;
use App\Filament\Resources\AparatKecamatans\Tables\AparatKecamatansTable;
use App\Models\AparatKecamatan;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AparatKecamatanResource extends Resource
{
    protected static ?string $model = AparatKecamatan::class;

    protected static bool $isScopedToTenant = false;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $modelLabel = 'Aparat Kecamatan';

    protected static ?string $pluralModelLabel = 'Aparat Kecamatan';

    protected static ?string $slug = 'aparat-kecamatan';

    protected static ?string $recordTitleAttribute = 'nama_lengkap';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return AparatKecamatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AparatKecamatansTable::configure($table);
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
            'index' => ListAparatKecamatans::route('/'),
            'create' => CreateAparatKecamatan::route('/create'),
            'edit' => EditAparatKecamatan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        $user = Auth::user();

        // Jika bukan Super Admin, filter berdasarkan kecamatan user
        if (! $user?->isSuperAdmin()) {
            $firstDesa = $user?->desas()->where('slug', '!=', 'semua-desa')->first();

            if ($firstDesa && $firstDesa->kecamatan_id) {
                $query->where('kecamatan_id', $firstDesa->kecamatan_id);
            }
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
