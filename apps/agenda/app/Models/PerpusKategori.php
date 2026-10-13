<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerpusKategori extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'perpus_kategori';

    protected $fillable = [
        'nama_kategori',
        'slug',
        'ikon',
        'keterangan',
    ];

    public function buku()
    {
        return $this->hasMany(PerpusBuku::class, 'kategori_id');
    }
}
