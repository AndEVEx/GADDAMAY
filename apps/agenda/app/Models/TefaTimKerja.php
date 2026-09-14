<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TefaTimKerja extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tefa_tim_kerja';

    protected $fillable = [
        'tefa_order_id',
        'siswa_id',
        'peran_dalam_tim',
        'job_desc',
        'status_tim',
    ];

    public function order()
    {
        return $this->belongsTo(TefaOrder::class, 'tefa_order_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}