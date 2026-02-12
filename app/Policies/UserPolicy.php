<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Super Admin tidak bisa diedit oleh Petugas Kecamatan
        if ($model->isSuperAdmin() && $user->isPetugasKecamatan()) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Super Admin tidak bisa dihapus oleh Petugas Kecamatan
        if ($model->isSuperAdmin() && $user->isPetugasKecamatan()) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // Super Admin tidak bisa direstore oleh Petugas Kecamatan
        if ($model->isSuperAdmin() && $user->isPetugasKecamatan()) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Super Admin tidak bisa dihapus permanen oleh Petugas Kecamatan
        if ($model->isSuperAdmin() && $user->isPetugasKecamatan()) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isPetugasKecamatan();
    }
}
