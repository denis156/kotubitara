<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AparatKecamatan;
use App\Models\User;

class AparatKecamatanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Super Admin dan Petugas Kecamatan bisa akses resource aparat kecamatan
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AparatKecamatan $aparatKecamatan): bool
    {
        // Super Admin bisa lihat semua
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Petugas Kecamatan hanya bisa lihat aparat di kecamatan mereka
        if ($user->isPetugasKecamatan()) {
            $firstDesa = $user->desas()->where('slug', '!=', 'semua-desa')->first();

            return $firstDesa && $firstDesa->kecamatan_id === $aparatKecamatan->kecamatan_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya Super Admin dan Petugas Kecamatan yang bisa buat aparat kecamatan
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AparatKecamatan $aparatKecamatan): bool
    {
        // Super Admin bisa update semua
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Petugas Kecamatan hanya bisa update aparat di kecamatan mereka
        if ($user->isPetugasKecamatan()) {
            $firstDesa = $user->desas()->where('slug', '!=', 'semua-desa')->first();

            return $firstDesa && $firstDesa->kecamatan_id === $aparatKecamatan->kecamatan_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AparatKecamatan $aparatKecamatan): bool
    {
        // Super Admin bisa delete semua
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Petugas Kecamatan hanya bisa delete aparat di kecamatan mereka
        if ($user->isPetugasKecamatan()) {
            $firstDesa = $user->desas()->where('slug', '!=', 'semua-desa')->first();

            return $firstDesa && $firstDesa->kecamatan_id === $aparatKecamatan->kecamatan_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AparatKecamatan $aparatKecamatan): bool
    {
        // Sama seperti update
        return $this->update($user, $aparatKecamatan);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AparatKecamatan $aparatKecamatan): bool
    {
        // Hanya Super Admin dan Petugas Kecamatan yang bisa hapus permanen
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }
}
