<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaTp extends Model
{
    use HasFactory;

    protected $table = 'agenda_tp';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'agenda_harian_id',
        'tp_id',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function agendaHarian(): BelongsTo
    {
        return $this->belongsTo(AgendaHarian::class);
    }

    public function tujuanPembelajaran(): BelongsTo
    {
        return $this->belongsTo(TujuanPembelajaran::class, 'tp_id');
    }
}
