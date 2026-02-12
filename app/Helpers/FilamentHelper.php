<?php

declare(strict_types=1);

namespace App\Helpers;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class FilamentHelper
{
    /**
     * Get the default desa_id based on current tenant.
     * Returns null if tenant is "Semua Desa", otherwise returns tenant's desa_id.
     */
    public static function getDefaultDesaId(): ?int
    {
        $tenant = Filament::getTenant();

        // Jika tenant adalah "Semua Desa", return null (tidak ada default)
        if (! $tenant || $tenant->slug === 'semua-desa') {
            return null;
        }

        // Jika tenant adalah desa spesifik, return id desa tersebut
        return $tenant->id;
    }

    /**
     * Check if the current tenant is "Semua Desa".
     */
    public static function isSemuaDesaTenant(): bool
    {
        $tenant = Filament::getTenant();

        return $tenant && $tenant->slug === 'semua-desa';
    }

    /**
     * Check if desa_id field should be disabled.
     * Disabled if user is Petugas Desa (always locked to their desa).
     * Enabled for Petugas Kecamatan (can choose any desa).
     */
    public static function shouldDisableDesaField(): bool
    {
        return Auth::user()?->isPetugasDesa() ?? false;
    }
}
