<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JenisMutasi;
use App\Models\Desa;
use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MutasiPenduduk extends Model
{
    /** @use HasFactory<\Database\Factories\MutasiPendudukFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'penduduk_id',
        'desa_id',
        'jenis_mutasi',
        'tanggal_mutasi',
        'alamat_asal',
        'alamat_tujuan',
        'alasan',
        'no_surat_pindah',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jenis_mutasi' => JenisMutasi::class,
            'tanggal_mutasi' => 'date',
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
