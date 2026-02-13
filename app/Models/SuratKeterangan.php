<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JenisSuratKeterangan;
use App\Observers\SuratKeteranganObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(SuratKeteranganObserver::class)]
class SuratKeterangan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'desa_id',
        'penduduk_id',
        'kematian_id',
        'jenis_surat',
        'keperluan',
        'nama_pemohon',
        'nik_pemohon',
        'data_domisili',
        'data_usaha',
        'data_ekonomi',
        'data_pernikahan',
        'data_ahli_waris',
        'data_kematian',
        'data_tambahan',
        'data_pelapor',
        'ttd_pemohon',
        'foto_ttd_pemohon',
        'dokumen_pendukung',
        'no_surat',
        'tanggal_surat',
        'kepala_desa_id',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jenis_surat' => JenisSuratKeterangan::class,
            'tanggal_surat' => 'date',
            'data_domisili' => 'array',
            'data_usaha' => 'array',
            'data_ekonomi' => 'array',
            'data_pernikahan' => 'array',
            'data_ahli_waris' => 'array',
            'data_kematian' => 'array',
            'data_tambahan' => 'array',
            'data_pelapor' => 'array',
            'dokumen_pendukung' => 'array',
        ];
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function kepalaDesa(): BelongsTo
    {
        return $this->belongsTo(AparatDesa::class, 'kepala_desa_id');
    }

    public function kematian(): BelongsTo
    {
        return $this->belongsTo(Kematian::class, 'kematian_id');
    }
}
