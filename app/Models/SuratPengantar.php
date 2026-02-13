<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JenisSuratPengantar;
use App\Observers\SuratPengantarObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(SuratPengantarObserver::class)]
class SuratPengantar extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'desa_id',
        'penduduk_id',
        'kelahiran_id',
        'jenis_surat',
        'nama_pemohon',
        'nik_pemohon',
        'ditujukan_kepada',
        'keperluan',
        'data_skck',
        'data_nikah',
        'data_pindah',
        'data_dokumen',
        'data_kelahiran',
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
            'jenis_surat' => JenisSuratPengantar::class,
            'tanggal_surat' => 'date',
            'data_skck' => 'array',
            'data_nikah' => 'array',
            'data_pindah' => 'array',
            'data_dokumen' => 'array',
            'data_kelahiran' => 'array',
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

    public function kelahiran(): BelongsTo
    {
        return $this->belongsTo(Kelahiran::class, 'kelahiran_id');
    }
}
