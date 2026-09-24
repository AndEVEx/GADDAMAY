<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsTkaSoal extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'lms_tka_soal';

    protected $fillable = [
        'paket_id',
        'nomor_urut',
        'pertanyaan',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'pilihan_e',
        'kunci_jawaban',
        'pembahasan',
    ];

    protected $casts = [
        'nomor_urut' => 'integer',
    ];

    public function paket()
    {
        return $this->belongsTo(LmsTkaPaket::class, 'paket_id');
    }
}
