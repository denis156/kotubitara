<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class KecamatanFieldHelper
{
    /**
     * Get the default kecamatan_id based on user's desa.
     * Returns null if user is Super Admin (can choose any kecamatan).
     * Returns first desa's kecamatan_id for non-Super Admin users.
     */
    public static function getDefaultKecamatanId(): ?int
    {
        $user = Auth::user();

        // Super Admin bisa pilih kecamatan mana saja
        if ($user?->isSuperAdmin()) {
            return null;
        }

        // Non-Super Admin: ambil kecamatan dari desa pertama yang dikelola
        $firstDesa = $user?->desas()->where('slug', '!=', 'semua-desa')->first();

        return $firstDesa?->kecamatan_id;
    }

    /**
     * Check if kecamatan_id field should be disabled.
     * Disabled if user is NOT Super Admin (locked to their kecamatan).
     * Enabled only for Super Admin.
     */
    public static function shouldDisableKecamatanField(): bool
    {
        return ! Auth::user()?->isSuperAdmin() ?? true;
    }

    /**
     * Check if kecamatan_id field should be dehydrated.
     * Only dehydrated if user is Super Admin (can change kecamatan).
     * Not dehydrated for non-Super Admin (keep existing value).
     */
    public static function shouldDehydrateKecamatanField(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    /**
     * Get hint text for kecamatan field based on user role.
     * Returns 'Otomatis' for non-Super Admin, null for Super Admin.
     */
    public static function getKecamatanFieldHint(): ?string
    {
        return ! Auth::user()?->isSuperAdmin() ? 'Otomatis' : null;
    }
}
