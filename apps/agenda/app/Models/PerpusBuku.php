<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerpusBuku extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'perpus_buku';

    protected $fillable = [
        'kategori_id',
        'judul',
        'slug',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'isbn',
        'jumlah_halaman',
        'sinopsis',
        'cover_url',
        'file_pdf_url',
        'file_size_mb',
        'allow_offline_cache',
        'total_dibaca',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'jumlah_halaman' => 'integer',
        'file_size_mb' => 'decimal:2',
        'allow_offline_cache' => 'boolean',
        'total_dibaca' => 'integer',
        'is_published' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(PerpusKategori::class, 'kategori_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function riwayatBaca()
    {
        return $this->hasMany(PerpusRiwayatBaca::class, 'buku_id');
    }

    public function getCoverAssetAttribute(): string
    {
        if ($this->cover_url && (str_starts_with($this->cover_url, 'http://') || str_starts_with($this->cover_url, 'https://'))) {
            return $this->cover_url;
        }

        if ($this->cover_url && file_exists(public_path($this->cover_url))) {
            return asset($this->cover_url);
        }

        return 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&q=80&w=600';
    }
}
