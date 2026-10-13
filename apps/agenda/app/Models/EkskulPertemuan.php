<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EkskulPertemuan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ekskul_pertemuan';

    protected $fillable = [
        'ekskul_id',
        'tanggal_pertemuan',
        'jam_mulai',
        'jam_selesai',
        'topik_latihan',
        'deskripsi_kegiatan',
        'pencatat_user_id',
        'status_pertemuan',
        'foto_kegiatan',
    ];

    protected $casts = [
        'tanggal_pertemuan' => 'date',
    ];

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class, 'ekskul_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pencatat_user_id');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(EkskulPresensi::class, 'ekskul_pertemuan_id');
    }
}
