<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerduptarKatalog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'perduptar_katalog';

    protected $fillable = [
        'tipe',
        'kategori_tingkat',
        'bobot_poin',
        'pasal_rujukan',
        'nama_butir',
        'deskripsi_aturan',
        'sanksi_default',
        'is_aktif',
    ];

    protected $casts = [
        'bobot_poin' => 'integer',
        'is_aktif' => 'boolean',
    ];

    public function poinSiswa(): HasMany
    {
        return $this->hasMany(PerduptarPoinSiswa::class, 'katalog_id');
    }
}
