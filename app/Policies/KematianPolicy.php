<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Kematian;
use App\Models\User;

class KematianPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user bisa akses resource kematian
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kematian $kematian): bool
    {
        // Petugas kecamatan bisa lihat semua data kematian
        // Petugas desa hanya bisa lihat data kematian di desa mereka
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($kematian->desa_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Semua user bisa buat data kematian
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Kematian $kematian): bool
    {
        // Petugas kecamatan bisa update semua data kematian
        // Petugas desa hanya bisa update data kematian di desa mereka
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($kematian->desa_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kematian $kematian): bool
    {
        // Petugas kecamatan bisa delete semua data kematian
        // Petugas desa hanya bisa delete data kematian di desa mereka
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($kematian->desa_id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Kematian $kematian): bool
    {
        // Sama seperti update
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($kematian->desa_id)->exists();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Kematian $kematian): bool
    {
        // Hanya petugas kecamatan yang bisa hapus permanen
        return $user->isPetugasKecamatan();
    }
}
