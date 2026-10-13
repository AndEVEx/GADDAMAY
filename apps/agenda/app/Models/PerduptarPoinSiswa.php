<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerduptarPoinSiswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'perduptar_poin_siswa';

    protected $fillable = [
        'siswa_id',
        'katalog_id',
        'tipe',
        'bobot_poin',
        'pasal_rujukan',
        'nama_butir',
        'kronologi_kejadian',
        'lokasi_kejadian',
        'tanggal_kejadian',
        'petugas_user_id',
        'peran_petugas',
        'sanksi_diberikan',
        'status_tindakan',
        'surat_peringatan',
        'foto_bukti',
        'notif_ortu_terkirim',
    ];

    protected $casts = [
        'bobot_poin' => 'integer',
        'tanggal_kejadian' => 'date',
        'notif_ortu_terkirim' => 'boolean',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function katalog(): BelongsTo
    {
        return $this->belongsTo(PerduptarKatalog::class, 'katalog_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_user_id');
    }
}
