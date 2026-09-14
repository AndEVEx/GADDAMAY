<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TefaLogProduksi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tefa_log_produksi';

    protected $fillable = [
        'tefa_order_id',
        'siswa_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'durasi_menit',
        'ringkasan_pekerjaan',
        'alat_dan_bahan',
        'foto_progres',
        'status_qc',
        'catatan_instruktur',
        'diperiksa_oleh_id',
        'diperiksa_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'durasi_menit' => 'integer',
        'diperiksa_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(TefaOrder::class, 'tefa_order_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function pemeriksa()
    {
        return $this->belongsTo(User::class, 'diperiksa_oleh_id');
    }
}