<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Desa;
use App\Models\Penduduk;
use App\Observers\KartuKeluargaObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(KartuKeluargaObserver::class)]
class KartuKeluarga extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'no_kk',
        'kepala_keluarga_id',
        'desa_id',
        'alamat',
        'rt',
        'rw',
    ];

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function kepalaKeluarga(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'kepala_keluarga_id');
    }

    public function anggotaKeluarga(): HasMany
    {
        return $this->hasMany(Penduduk::class, 'kartu_keluarga_id');
    }
}
