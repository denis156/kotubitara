<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\KecamatanObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(KecamatanObserver::class)]
class Kecamatan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'kode_provinsi',
        'nama_provinsi',
        'kode_kabupaten',
        'nama_kabupaten',
        'kode_kecamatan',
        'nama_kecamatan',
        'alamat',
        'telepon',
        'email',
        'website',
    ];

    public function desas(): HasMany
    {
        return $this->hasMany(Desa::class);
    }

    public function aparatKecamatans(): HasMany
    {
        return $this->hasMany(AparatKecamatan::class);
    }
}
