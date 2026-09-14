<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TefaOrder extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tefa_order';

    protected $fillable = [
        'kode_order',
        'nama_pemesan',
        'nomor_wa_pemesan',
        'instansi_pemesan',
        'judul_proyek',
        'kategori_kejuruan',
        'biaya_proyek',
        'tanggal_masuk',
        'target_selesai',
        'tanggal_selesai_aktual',
        'status',
        'instruktur_id',
        'deskripsi_proyek',
        'catatan_proyek',
    ];

    protected $casts = [
        'biaya_proyek' => 'float',
        'tanggal_masuk' => 'date',
        'target_selesai' => 'date',
        'tanggal_selesai_aktual' => 'date',
    ];

    public function instruktur()
    {
        return $this->belongsTo(User::class, 'instruktur_id');
    }

    public function timKerja()
    {
        return $this->hasMany(TefaTimKerja::class, 'tefa_order_id');
    }

    public function logProduksi()
    {
        return $this->hasMany(TefaLogProduksi::class, 'tefa_order_id');
    }

    public function getTotalMenitProduksiAttribute(): int
    {
        return (int) $this->logProduksi()->where('status_qc', 'lolos_qc')->sum('durasi_menit');
    }

    public function getTotalJamProduksiAttribute(): float
    {
        return round($this->total_menit_produksi / 60, 1);
    }
}