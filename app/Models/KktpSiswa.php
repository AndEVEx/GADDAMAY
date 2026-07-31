<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KktpSiswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kktp_siswa';

    protected $fillable = [
        'agenda_harian_id',
        'siswa_id',
        'tp_id',
        'status',
    ];

    public function agendaHarian(): BelongsTo
    {
        return $this->belongsTo(AgendaHarian::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tujuanPembelajaran(): BelongsTo
    {
        return $this->belongsTo(TujuanPembelajaran::class, 'tp_id');
    }

    public function isTercapai(): bool
    {
        return $this->status === 'tercapai';
    }
}
