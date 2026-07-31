<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    public function jadwalGuru(): HasMany
    {
        return $this->hasMany(JadwalGuru::class, 'guru_id');
    }

    public function agendaHarian(): HasMany
    {
        return $this->hasMany(AgendaHarian::class, 'guru_id');
    }

    public function tujuanPembelajaran(): HasMany
    {
        return $this->hasMany(TujuanPembelajaran::class, 'ketua_mgmp_id');
    }

    // =========================================================================
    // Role Helpers
    // =========================================================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKepsek(): bool
    {
        return $this->role === 'kepsek';
    }

    public function isWaka(): bool
    {
        return $this->role === 'waka';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isKetuaMgmp(): bool
    {
        return $this->role === 'ketua_mgmp';
    }

    public function isKetuaKelas(): bool
    {
        return $this->role === 'ketua_kelas';
    }

    public function canOverride(): bool
    {
        return in_array($this->role, ['admin', 'waka']);
    }
}
