<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Models\Desa;
use App\Observers\UserObserver;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements FilamentUser, HasAvatar, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telepon',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function desas(): BelongsToMany
    {
        return $this->belongsToMany(Desa::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        // Petugas Kecamatan bisa akses semua desa + opsi "Semua Desa"
        if ($this->isPetugasKecamatan()) {
            // Get special "Semua Desa" tenant dan semua desa real
            $semuaDesa = Desa::where('slug', 'semua-desa')->first();
            $allDesas = Desa::where('slug', '!=', 'semua-desa')->get();

            // Put "Semua Desa" di posisi pertama
            if ($semuaDesa) {
                return collect([$semuaDesa])->merge($allDesas);
            }

            return $allDesas;
        }

        // Petugas Desa hanya bisa akses desa mereka
        return $this->desas;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        // Petugas Kecamatan bisa akses semua desa termasuk "Semua Desa"
        if ($this->isPetugasKecamatan()) {
            return true;
        }

        // Petugas Desa hanya bisa akses desa mereka, TIDAK bisa akses "Semua Desa"
        if ($tenant->slug === 'semua-desa') {
            return false;
        }

        return $this->desas()->whereKey($tenant)->exists();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function isPetugasKecamatan(): bool
    {
        return $this->role === UserRole::PETUGAS_KECAMATAN;
    }

    public function isPetugasDesa(): bool
    {
        return $this->role === UserRole::PETUGAS_DESA;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
}
