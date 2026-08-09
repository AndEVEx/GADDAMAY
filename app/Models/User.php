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

    /**
     * Normalize human-readable role strings to valid database ENUM values.
     */
    public static function normalizeRole(?string $role): string
    {
        if (empty($role)) {
            return 'guru';
        }

        $normalized = strtolower(trim($role));
        $normalized = str_replace([' ', '-'], '_', $normalized);

        return match ($normalized) {
            'admin', 'administrator' => 'admin',
            'kepsek', 'kepala_sekolah', 'headmaster' => 'kepsek',
            'waka', 'wakil_kepala_sekolah', 'wakasek' => 'waka',
            'ketua_mgmp', 'mgmp' => 'ketua_mgmp',
            'ketua_kelas', 'km', 'ketua' => 'ketua_kelas',
            'guru', 'pengajar', 'teacher' => 'guru',
            default => in_array($normalized, ['admin', 'kepsek', 'waka', 'ketua_mgmp', 'guru', 'ketua_kelas']) ? $normalized : 'guru',
        };
    }

    /**
     * Mutator for role attribute to auto-normalize role strings.
     */
    protected function setRoleAttribute($value): void
    {
        $this->attributes['role'] = self::normalizeRole($value);
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
