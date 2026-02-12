<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JabatanAparat;
use App\Models\Desa;
use App\Observers\AparatDesaObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(AparatDesaObserver::class)]
class AparatDesa extends Model
{
    /** @use HasFactory<\Database\Factories\AparatDesaFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'desa_id',
        'nama_lengkap',
        'nip',
        'jabatan',
        'nama_dusun',
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
            'jabatan' => JabatanAparat::class,
            'tanggal_mulai_jabatan' => 'date',
            'tanggal_selesai_jabatan' => 'date',
        ];
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }
}
