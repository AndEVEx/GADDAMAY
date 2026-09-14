<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjikomBerkasAsesmen extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ujikom_berkas_asesmen';

    protected $fillable = [
        'pendaftaran_id',
        'jenis_berkas',
        'nama_file',
        'file_path',
        'is_valid',
    ];

    protected $casts = [
        'is_valid' => 'boolean',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(UjikomPendaftaran::class, 'pendaftaran_id');
    }
}