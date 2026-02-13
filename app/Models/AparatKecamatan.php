<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JabatanAparatKecamatan;
use App\Observers\AparatKecamatanObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(AparatKecamatanObserver::class)]
class AparatKecamatan extends Model
{
    /** @use HasFactory<\Database\Factories\AparatKecamatanFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'kecamatan_id',
        'nama_lengkap',
        'nip',
        'jabatan',
        'telepon',
        'email',
        'alamat',
        'tanggal_mulai_jabatan',
        'tanggal_selesai_jabatan',
        'status',
        'foto',
        'ttd_digital',
        'foto_ttd',
    ];

    protected function casts(): array
    {
        return [
            'jabatan' => JabatanAparatKecamatan::class,
            'tanggal_mulai_jabatan' => 'date',
            'tanggal_selesai_jabatan' => 'date',
        ];
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
