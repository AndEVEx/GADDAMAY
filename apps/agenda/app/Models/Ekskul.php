<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ekskul extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ekskul';

    protected $fillable = [
        'nama_ekskul',
        'slug',
        'kategori',
        'deskripsi',
        'jadwal_rutin',
        'tempat_latihan',
        'pembina_guru_id',
        'pelatih_nama',
        'pelatih_user_id',
        'kontak_pelatih',
        'logo_ikon',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function pembina(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembina_guru_id');
    }

    public function pelatihUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelatih_user_id');
    }

    public function anggota(): HasMany
    {
        return $this->hasMany(EkskulAnggota::class, 'ekskul_id');
    }

    public function pertemuan(): HasMany
    {
        return $this->hasMany(EkskulPertemuan::class, 'ekskul_id');
    }

    public function getNamaPembinaLengkapAttribute(): string
    {
        return $this->pembina->name ?? 'Belum Ditentukan';
    }

    public function getNamaPelatihLengkapAttribute(): string
    {
        return $this->pelatih_nama ?: ($this->pelatihUser->name ?? 'Belum Ditentukan');
    }
}
