<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Kecamatan;
use App\Models\User;

class KecamatanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Super Admin dan Petugas Kecamatan bisa akses menu kecamatan
        // Petugas Kecamatan akan langsung redirect ke edit di ListKecamatans
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kecamatan $kecamatan): bool
    {
        // Super Admin dan Petugas Kecamatan bisa view detail kecamatan
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya Super Admin yang bisa create kecamatan
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Kecamatan $kecamatan): bool
    {
        // Super Admin dan Petugas Kecamatan bisa update kecamatan
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kecamatan $kecamatan): bool
    {
        // Hanya Super Admin yang bisa delete kecamatan
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Kecamatan $kecamatan): bool
    {
        // Hanya Super Admin yang bisa restore kecamatan
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Kecamatan $kecamatan): bool
    {
        // Hanya Super Admin yang bisa force delete kecamatan
        return $user->isSuperAdmin();
    }
}
