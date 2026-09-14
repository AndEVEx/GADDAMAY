<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TujuanPembelajaran extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'tujuan_pembelajaran';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'mapel_id',
        'tingkat',
        'kode_tp',
        'deskripsi_tp',
        'order_sequence',
        'ketua_mgmp_id',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function ketuaMgmp(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ketua_mgmp_id');
    }

    public function agendaTp(): HasMany
    {
        return $this->hasMany(AgendaTp::class, 'tp_id');
    }
}
