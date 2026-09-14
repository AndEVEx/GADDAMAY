<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalSwapLog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jadwal_swap_logs';

    protected $fillable = [
        'batch_id',
        'user_id',
        'swap_type',
        'rombel_a_id',
        'rombel_b_id',
        'rombel_a_nama',
        'rombel_b_nama',
        'schedules_count_a',
        'schedules_count_b',
        'details',
        'status',
        'undone_at',
        'undone_by_id',
    ];

    protected $casts = [
        'details' => 'array',
        'undone_at' => 'datetime',
        'schedules_count_a' => 'integer',
        'schedules_count_b' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function undoneBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'undone_by_id');
    }

    public function rombelA(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_a_id');
    }

    public function rombelB(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_b_id');
    }
}
