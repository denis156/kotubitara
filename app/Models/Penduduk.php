<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Agama;
use App\Enums\HubunganKeluarga;
use App\Enums\JenisKelamin;
use App\Enums\Kewarganegaraan;
use App\Enums\StatusPerkawinan;
use App\Models\Desa;
use App\Models\KartuKeluarga;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penduduk extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'nik',
        'kartu_keluarga_id',
        'desa_id',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'status_perkawinan',
        'hubungan_keluarga',
        'pekerjaan',
        'pendidikan',
        'nama_ayah',
        'nama_ibu',
        'kewarganegaraan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'jenis_kelamin' => JenisKelamin::class,
            'agama' => Agama::class,
            'status_perkawinan' => StatusPerkawinan::class,
            'hubungan_keluarga' => HubunganKeluarga::class,
            'kewarganegaraan' => Kewarganegaraan::class,
        ];
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class);
    }
}
