<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PklJurnalHarian extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pkl_jurnal_harian';

    // 11 Elemen Capaian Pembelajaran (CP) Konsentrasi Keahlian PPLG SMKN 2 Indramayu 2025
    public const ELEMEN_CP = [
        'K3LH & Budaya Kerja Industri',
        'Proses Bisnis & Pengembangan Perangkat Lunak',
        'Perancangan Antarmuka & UX/UI Desain',
        'Pemrograman Berorientasi Objek (OOP)',
        'Pemrograman Web (Frontend & Backend Framework)',
        'Pemrograman Perangkat Bergerak (Mobile Application)',
        'Pengelolaan & Desain Basis Data (RDBMS/NoSQL)',
        'Pengujian Perangkat Lunak (Software Quality Assurance)',
        'DevOps, Version Control (Git) & Cloud Deployment',
        'Keamanan Sistem & Cyber Security Dasar',
        'Komunikasi Profesional & Presentasi Produk Perangkat Lunak',
    ];

    protected $fillable = [
        'penempatan_id',
        'presensi_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'ringkasan_pekerjaan',
        'alat_dan_bahan',
        'elemen_cp',
        'foto_dokumentasi',
        'paraf_dudi_status',
        'catatan_dudi',
        'paraf_dudi_at',
        'paraf_guru_status',
        'catatan_guru',
        'paraf_guru_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'paraf_dudi_at' => 'datetime',
        'paraf_guru_at' => 'datetime',
    ];

    public function penempatan()
    {
        return $this->belongsTo(PklPenempatan::class, 'penempatan_id');
    }

    public function presensi()
    {
        return $this->belongsTo(PklPresensi::class, 'presensi_id');
    }
}