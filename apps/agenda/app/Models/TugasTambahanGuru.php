<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasTambahanGuru extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tugas_tambahan_guru';

    protected $fillable = [
        'guru_id',
        'jenis_tugas',
        'rombel_id',
        'tahun_ajaran',
        'sk_penugasan',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public static function getLabelJenisTugas(?string $jenis): string
    {
        return match ($jenis) {
            'wali_kelas' => 'Wali Kelas',
            'pembina_kesiswaan' => 'Pembina Kesiswaan',
            'guru_bk' => 'Guru BK (Bimbingan Konseling)',
            'guru_piket' => 'Guru Piket Sekolah',
            'koordinator_literasi' => 'Koordinator Literasi',
            'pembina_ekskul' => 'Pembina Ekstrakurikuler',
            'kepala_lab' => 'Kepala Laboratorium / Bengkel',
            default => ucwords(str_replace('_', ' ', $jenis ?? '-')),
        };
    }
}
