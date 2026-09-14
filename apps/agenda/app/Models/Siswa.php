<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siswa';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'rombel_id',
        'nis',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function kehadiranMurid(): HasMany
    {
        return $this->hasMany(KehadiranMurid::class);
    }
}
