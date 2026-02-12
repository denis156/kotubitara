<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MutasiPenduduk;
use App\Models\User;

class MutasiPendudukPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user bisa akses resource mutasi penduduk
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MutasiPenduduk $mutasiPenduduk): bool
    {
        // Petugas kecamatan bisa lihat semua data mutasi penduduk
        // Petugas desa hanya bisa lihat data mutasi penduduk di desa mereka
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($mutasiPenduduk->desa_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Semua user bisa buat data mutasi penduduk
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MutasiPenduduk $mutasiPenduduk): bool
    {
        // Petugas kecamatan bisa update semua data mutasi penduduk
        // Petugas desa hanya bisa update data mutasi penduduk di desa mereka
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($mutasiPenduduk->desa_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MutasiPenduduk $mutasiPenduduk): bool
    {
        // Petugas kecamatan bisa delete semua data mutasi penduduk
        // Petugas desa hanya bisa delete data mutasi penduduk di desa mereka
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($mutasiPenduduk->desa_id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MutasiPenduduk $mutasiPenduduk): bool
    {
        // Sama seperti update
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($mutasiPenduduk->desa_id)->exists();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MutasiPenduduk $mutasiPenduduk): bool
    {
        // Hanya petugas kecamatan yang bisa hapus permanen
        return $user->isPetugasKecamatan();
    }
}
