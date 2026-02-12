<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User;
use App\Observers\DesaObserver;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(DesaObserver::class)]
class Desa extends Model implements HasName
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'desas';

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    protected $fillable = [
        'kecamatan_id',
        'nama_desa',
        'slug',
        'kode_desa',
        'alamat',
        'telepon',
        'email',
    ];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function getFilamentName(): string
    {
        return $this->nama_desa ?? '';
    }
}
