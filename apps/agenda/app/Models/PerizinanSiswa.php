<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerizinanSiswa extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'perizinan_siswa';

    protected $fillable = [
        'siswa_id',
        'kategori',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'alasan',
        'file_lampiran',
        'nama_pemohon',
        'nomor_wa_pemohon',
        'hubungan_pemohon',
        'status',
        'diverifikasi_oleh_id',
        'peran_verifikator',
        'catatan_verifikator',
        'waktu_verifikasi',
        'auto_locked_agenda',
        'sync_gate_status',
        'wa_notif_status',
        'wa_notif_text',
        'wa_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'waktu_verifikasi' => 'datetime',
            'wa_sent_at' => 'datetime',
            'auto_locked_agenda' => 'boolean',
            'sync_gate_status' => 'boolean',
        ];
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh_id');
    }

    // =========================================================================
    // Helpers & Formatters
    // =========================================================================

    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'sakit' => 'Sakit (Surat Dokter / Ortu)',
            'izin_keperluan' => 'Izin Keperluan Keluarga',
            'dispensasi_sekolah' => 'Dispensasi Tugas / Lomba Sekolah',
            'izin_keluar_kampus' => 'Izin Keluar Lingkungan Sekolah',
            default => ucfirst(str_replace('_', ' ', $this->kategori)),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu Verifikasi',
            'disetujui_walas' => 'Disetujui Wali Kelas',
            'disetujui_piket' => 'Disetujui Guru Piket',
            'disetujui_bk' => 'Disetujui Guru BK',
            'ditolak' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function isDisetujui(): bool
    {
        return in_array($this->status, ['disetujui_walas', 'disetujui_piket', 'disetujui_bk']);
    }

    /**
     * Generate format teks WhatsApp resmi ke Orang Tua / Pemohon
     */
    public function generateWaMessage(): string
    {
        $namaSiswa = $this->siswa?->nama_siswa ?? 'Siswa';
        $kelas = $this->siswa?->rombel?->nama_kelas ?? '-';
        $kategori = $this->kategori_label;
        $tglMulai = $this->tanggal_mulai ? $this->tanggal_mulai->translatedFormat('d F Y') : '-';
        $tglSelesai = $this->tanggal_selesai ? $this->tanggal_selesai->translatedFormat('d F Y') : '-';
        $rentang = ($this->tanggal_mulai == $this->tanggal_selesai) ? $tglMulai : "{$tglMulai} s.d. {$tglSelesai}";
        
        $statusTeks = $this->isDisetujui() ? "TELAH DISETUJUI" : "DITOLAK";
        $verifikator = $this->verifikator?->name ?? 'Petugas Sekolah';
        $peran = ucwords(str_replace('_', ' ', $this->peran_verifikator ?? 'Petugas'));

        $jamKet = '';
        if ($this->kategori === 'izin_keluar_kampus' && $this->jam_mulai) {
            $jamKet = "\n⏰ Waktu Keluar: {$this->jam_mulai} - " . ($this->jam_selesai ?? 'Selesai');
        }

        $catatan = !empty($this->catatan_verifikator) ? "\n💬 Catatan: {$this->catatan_verifikator}" : "";

        return "*KONFIRMASI PERIZINAN SISWA - SMKN 2 INDRAMAYU*\n\n" .
               "Yth. Orang Tua / Wali dari ananda:\n" .
               "👤 *Nama:* {$namaSiswa}\n" .
               "🏫 *Kelas:* {$kelas}\n" .
               "📋 *Kategori Izin:* {$kategori}\n" .
               "📅 *Tanggal:* {$rentang}{$jamKet}\n" .
               "📝 *Alasan:* {$this->alasan}\n\n" .
               "Status Pengajuan: *{$statusTeks}*\n" .
               "Diverifikasi oleh: {$peran} ({$verifikator}){$catatan}\n\n" .
               "Kehadiran ananda telah tercatat resmi pada sistem presensi dan buku agenda guru kelas.\n\n" .
               "_Pesan otomatis dari Sistem Terpadu GADDAMAY SMKN 2 Indramayu_";
    }

    /**
     * Generate Link WhatsApp Web / App (wa.me)
     */
    public function generateWaMeLink(): ?string
    {
        $phone = $this->nomor_wa_pemohon;
        if (empty($phone)) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }

        $msg = rawurlencode($this->generateWaMessage());
        return "https://wa.me/{$clean}?text={$msg}";
    }
}