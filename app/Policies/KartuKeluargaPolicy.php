<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\KartuKeluarga;
use App\Models\User;

class KartuKeluargaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user bisa akses resource kartu keluarga
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KartuKeluarga $kartuKeluarga): bool
    {
        // Super Admin dan Petugas kecamatan bisa lihat semua kartu keluarga
        // Petugas desa hanya bisa lihat kartu keluarga di desa mereka
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($kartuKeluarga->desa_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Semua user bisa buat data kartu keluarga
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KartuKeluarga $kartuKeluarga): bool
    {
        // Super Admin dan Petugas kecamatan bisa update semua kartu keluarga
        // Petugas desa hanya bisa update kartu keluarga di desa mereka
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($kartuKeluarga->desa_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KartuKeluarga $kartuKeluarga): bool
    {
        // Super Admin dan Petugas kecamatan bisa delete semua kartu keluarga
        // Petugas desa hanya bisa delete kartu keluarga di desa mereka
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($kartuKeluarga->desa_id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, KartuKeluarga $kartuKeluarga): bool
    {
        // Sama seperti update
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($kartuKeluarga->desa_id)->exists();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, KartuKeluarga $kartuKeluarga): bool
    {
        // Hanya Super Admin dan petugas kecamatan yang bisa hapus permanen
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }
}
