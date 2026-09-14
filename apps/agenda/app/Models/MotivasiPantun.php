<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MotivasiPantun extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'motivasi_pantun';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'isi',
        'tipe',
        'kategori',
    ];

    // =========================================================================
    // Scopes
    // =========================================================================

    /**
     * Scope to filter motivasi for "sebelum mengajar" category.
     */
    public function scopeSebelumMengajar(Builder $query): Builder
    {
        return $query->where('kategori', 'sebelum_mengajar');
    }

    /**
     * Scope to filter motivasi for "siap mengajar" category.
     */
    public function scopeSiapMengajar(Builder $query): Builder
    {
        return $query->where('kategori', 'siap_mengajar');
    }

    /**
     * Scope to filter pantun type.
     */
    public function scopePantun(Builder $query): Builder
    {
        return $query->where('tipe', 'pantun');
    }

    /**
     * Scope to filter kata mutiara type.
     */
    public function scopeKataMutiara(Builder $query): Builder
    {
        return $query->where('tipe', 'kata_mutiara');
    }
}
