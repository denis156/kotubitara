<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Penduduk;
use App\Models\User;

class PendudukPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user bisa akses resource penduduk
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Penduduk $penduduk): bool
    {
        // Super Admin dan Petugas kecamatan bisa lihat semua penduduk
        // Petugas desa hanya bisa lihat penduduk di desa mereka
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($penduduk->desa_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Semua user bisa buat data penduduk
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Penduduk $penduduk): bool
    {
        // Super Admin dan Petugas kecamatan bisa update semua penduduk
        // Petugas desa hanya bisa update penduduk di desa mereka
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($penduduk->desa_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Penduduk $penduduk): bool
    {
        // Super Admin dan Petugas kecamatan bisa delete semua penduduk
        // Petugas desa hanya bisa delete penduduk di desa mereka
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($penduduk->desa_id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Penduduk $penduduk): bool
    {
        // Sama seperti update
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($penduduk->desa_id)->exists();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Penduduk $penduduk): bool
    {
        // Hanya Super Admin dan petugas kecamatan yang bisa hapus permanen
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }
}
