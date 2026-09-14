<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KehadiranMurid extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kehadiran_murid';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'agenda_harian_id',
        'siswa_id',
        'status',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function agendaHarian(): BelongsTo
    {
        return $this->belongsTo(AgendaHarian::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
