<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerpusRiwayatBaca extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'perpus_riwayat_baca';

    protected $fillable = [
        'buku_id',
        'user_id',
        'siswa_id',
        'rombel_id',
        'halaman_terakhir',
        'persentase_baca',
        'durasi_detik',
        'waktu_mulai_baca',
        'waktu_terakhir_baca',
        'status_selesai',
        'is_offline_synced',
        'catatan_baca',
    ];

    protected $casts = [
        'halaman_terakhir' => 'integer',
        'persentase_baca' => 'decimal:2',
        'durasi_detik' => 'integer',
        'waktu_mulai_baca' => 'datetime',
        'waktu_terakhir_baca' => 'datetime',
        'status_selesai' => 'boolean',
        'is_offline_synced' => 'boolean',
    ];

    public function buku()
    {
        return $this->belongsTo(PerpusBuku::class, 'buku_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function getDurasiFormatAttribute(): string
    {
        $menit = floor($this->durasi_detik / 60);
        $detik = $this->durasi_detik % 60;
        if ($menit >= 60) {
            $jam = floor($menit / 60);
            $sisaMenit = $menit % 60;
            return "{$jam} jam {$sisaMenit} mnt";
        }
        return "{$menit} mnt {$detik} dtk";
    }
}
