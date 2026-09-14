<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentingCatatanDisiplin extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'parenting_catatan_disiplin';

    protected $fillable = [
        'siswa_id',
        'kategori',
        'jenis_tindakan',
        'poin',
        'deskripsi',
        'petugas_id',
        'peran_petugas',
        'foto_bukti',
        'tanggal_kejadian',
        'notif_wa_ortu_status',
        'wa_sent_at',
    ];

    protected $casts = [
        'poin' => 'integer',
        'tanggal_kejadian' => 'date',
        'wa_sent_at' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}