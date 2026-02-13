<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\HubunganPelapor;
use App\Enums\JenisKelamin;
use App\Models\Desa;
use App\Models\Penduduk;
use App\Observers\KelahiranObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(KelahiranObserver::class)]
class Kelahiran extends Model
{
    /** @use HasFactory<\Database\Factories\KelahiranFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'desa_id',
        'nama_bayi',
        'nik_bayi',
        'jenis_kelamin',
        'tanggal_lahir',
        'waktu_lahir',
        'tempat_lahir',
        'berat_lahir',
        'panjang_lahir',
        'ayah_id',
        'ibu_id',
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
            'jenis_kelamin' => JenisKelamin::class,
            'tanggal_lahir' => 'date',
            'waktu_lahir' => 'datetime:H:i',
            'berat_lahir' => 'decimal:2',
            'panjang_lahir' => 'decimal:2',
            'hubungan_pelapor' => HubunganPelapor::class,
        ];
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function ayah(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'ayah_id');
    }

    public function ibu(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'ibu_id');
    }
}
