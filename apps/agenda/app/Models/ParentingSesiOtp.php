<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentingSesiOtp extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'parenting_sesi_otp';

    protected $fillable = [
        'siswa_id',
        'nomor_wa_ortu',
        'otp_code',
        'magic_token',
        'expires_at',
        'is_used',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }
}