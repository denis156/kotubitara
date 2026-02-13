<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\HubunganPelapor;
use App\Models\Desa;
use App\Models\Penduduk;
use App\Observers\KematianObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(KematianObserver::class)]
class Kematian extends Model
{
    /** @use HasFactory<\Database\Factories\KematianFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'penduduk_id',
        'desa_id',
        'tanggal_meninggal',
        'waktu_meninggal',
        'tempat_meninggal',
        'sebab_kematian',
        'tempat_pemakaman',
        'tanggal_pemakaman',
        'nama_pelapor',
        'nik_pelapor',
        'hubungan_pelapor',
        'alamat_pelapor',
        'telepon_pelapor',
        'foto_surat_rs',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_meninggal' => 'date',
            'tanggal_pemakaman' => 'date',
            'hubungan_pelapor' => HubunganPelapor::class,
        ];
    }

    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

}
