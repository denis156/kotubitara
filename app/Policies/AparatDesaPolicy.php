<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AparatDesa;
use App\Models\User;

class AparatDesaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user bisa akses resource aparat desa
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AparatDesa $aparatDesa): bool
    {
        // Petugas kecamatan bisa lihat semua data aparat desa
        // Petugas desa hanya bisa lihat data aparat desa di desa mereka
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($aparatDesa->desa_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Semua user bisa buat data aparat desa
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AparatDesa $aparatDesa): bool
    {
        // Petugas kecamatan bisa update semua data aparat desa
        // Petugas desa hanya bisa update data aparat desa di desa mereka
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($aparatDesa->desa_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AparatDesa $aparatDesa): bool
    {
        // Petugas kecamatan bisa delete semua data aparat desa
        // Petugas desa hanya bisa delete data aparat desa di desa mereka
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($aparatDesa->desa_id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AparatDesa $aparatDesa): bool
    {
        // Sama seperti update
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($aparatDesa->desa_id)->exists();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AparatDesa $aparatDesa): bool
    {
        // Hanya petugas kecamatan yang bisa hapus permanen
        return $user->isPetugasKecamatan();
    }
}
