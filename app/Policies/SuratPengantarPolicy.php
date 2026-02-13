<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SuratPengantar;
use App\Models\User;

class SuratPengantarPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user bisa akses resource surat pengantar
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SuratPengantar $suratPengantar): bool
    {
        // Super Admin dan Petugas kecamatan bisa lihat semua data
        // Petugas desa hanya bisa lihat data di desa mereka
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($suratPengantar->desa_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Semua user bisa buat surat pengantar
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SuratPengantar $suratPengantar): bool
    {
        // Super Admin dan Petugas kecamatan bisa update semua data
        // Petugas desa hanya bisa update data di desa mereka
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($suratPengantar->desa_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SuratPengantar $suratPengantar): bool
    {
        // Super Admin dan Petugas kecamatan bisa delete semua data
        // Petugas desa hanya bisa delete data di desa mereka
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($suratPengantar->desa_id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SuratPengantar $suratPengantar): bool
    {
        // Sama seperti update
        return $user->isSuperAdmin() || $user->isPetugasKecamatan() || $user->desas()->whereKey($suratPengantar->desa_id)->exists();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SuratPengantar $suratPengantar): bool
    {
        // Hanya Super Admin dan petugas kecamatan yang bisa hapus permanen
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }
}
