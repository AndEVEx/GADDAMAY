<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiKktp extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'nilai_kktp';

    protected $fillable = [
        'siswa_id',
        'tp_id',
        'rombel_id',
        'guru_id',
        'status',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tujuanPembelajaran(): BelongsTo
    {
        return $this->belongsTo(TujuanPembelajaran::class, 'tp_id');
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function isTercapai(): bool
    {
        return $this->status === 'tercapai';
    }
}
