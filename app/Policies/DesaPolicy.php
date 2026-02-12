<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Desa;
use App\Models\User;

class DesaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user bisa akses resource desa
        // Redirect logic ada di ListDesas page untuk petugas desa
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Desa $desa): bool
    {
        // Desa "Semua Desa" (special tenant) bisa dilihat oleh semua user
        if ($desa->slug === 'semua-desa') {
            return true;
        }

        // Petugas kecamatan bisa lihat semua desa
        // Petugas desa hanya bisa lihat desa yang mereka akses
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($desa->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya petugas kecamatan yang bisa buat desa baru
        return $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Desa $desa): bool
    {
        // TIDAK BOLEH update desa "Semua Desa" (special tenant) oleh siapapun
        if ($desa->slug === 'semua-desa') {
            return false;
        }

        // Petugas kecamatan bisa update semua desa
        // Petugas desa hanya bisa update desa yang mereka akses
        return $user->isPetugasKecamatan() || $user->desas()->whereKey($desa->id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Desa $desa): bool
    {
        // TIDAK BOLEH hapus desa "Semua Desa" (special tenant) oleh siapapun
        if ($desa->slug === 'semua-desa') {
            return false;
        }

        // Hanya petugas kecamatan yang bisa hapus desa
        return $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Desa $desa): bool
    {
        // TIDAK BOLEH restore desa "Semua Desa" (special tenant) oleh siapapun
        if ($desa->slug === 'semua-desa') {
            return false;
        }

        // Hanya petugas kecamatan yang bisa restore desa
        return $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Desa $desa): bool
    {
        // TIDAK BOLEH force delete desa "Semua Desa" (special tenant) oleh siapapun
        if ($desa->slug === 'semua-desa') {
            return false;
        }

        // Hanya petugas kecamatan yang bisa hapus permanen desa
        return $user->isPetugasKecamatan();
    }
}
