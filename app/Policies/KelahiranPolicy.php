<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Kelahiran;
use App\Models\User;

class KelahiranPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user bisa akses resource kelahiran
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kelahiran $kelahiran): bool
    {
        // Super Admin dan Petugas kecamatan bisa lihat semua data kelahiran
        // Petugas desa hanya bisa lihat data kelahiran di desa mereka
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($kelahiran->desa_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Semua user bisa buat data kelahiran
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Kelahiran $kelahiran): bool
    {
        // Super Admin dan Petugas kecamatan bisa update semua data kelahiran
        // Petugas desa hanya bisa update data kelahiran di desa mereka
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($kelahiran->desa_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kelahiran $kelahiran): bool
    {
        // Super Admin dan Petugas kecamatan bisa delete semua data kelahiran
        // Petugas desa hanya bisa delete data kelahiran di desa mereka
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($kelahiran->desa_id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Kelahiran $kelahiran): bool
    {
        // Sama seperti update
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($kelahiran->desa_id)->exists();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Kelahiran $kelahiran): bool
    {
        // Hanya Super Admin dan petugas kecamatan yang bisa hapus permanen
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }
}
