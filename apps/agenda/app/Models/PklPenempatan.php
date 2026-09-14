<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PklPenempatan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pkl_penempatan';

    protected $fillable = [
        'siswa_id',
        'dudi_id',
        'guru_pembimbing_id',
        'tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'nama_pembimbing_dudi',
        'nomor_wa_dudi',
        'token_magic_link_dudi',
        'status',
        'catatan_penempatan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->token_magic_link_dudi)) {
                $model->token_magic_link_dudi = Str::random(48);
            }
        });
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function dudi()
    {
        return $this->belongsTo(PklDudi::class, 'dudi_id');
    }

    public function guruPembimbing()
    {
        return $this->belongsTo(User::class, 'guru_pembimbing_id');
    }

    public function presensi()
    {
        return $this->hasMany(PklPresensi::class, 'penempatan_id');
    }

    public function jurnalHarian()
    {
        return $this->hasMany(PklJurnalHarian::class, 'penempatan_id');
    }

    public function penilaian()
    {
        return $this->hasOne(PklPenilaian::class, 'penempatan_id');
    }

    public function getMagicLinkUrlAttribute(): string
    {
        return url('/pkl/review-dudi/' . $this->token_magic_link_dudi);
    }
}