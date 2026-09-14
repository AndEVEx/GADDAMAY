<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Master Dunia Usaha / Dunia Industri (DUDI)
        Schema::create('pkl_dudi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_instansi', 150);
            $table->string('bidang_usaha', 100)->nullable();
            $table->text('alamat');
            $table->string('kota', 50)->default('Indramayu');
            $table->string('pimpinan_nama', 100)->nullable();
            $table->string('pimpinan_jabatan', 100)->nullable();
            $table->string('pembimbing_nama', 100)->nullable();
            $table->string('pembimbing_kontak', 25)->nullable(); // Nomor HP / WA DUDI
            $table->decimal('latitude', 10, 8)->nullable(); // Koordinat GPS DUDI
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedInteger('radius_meter')->default(100); // Toleransi radius presensi meter
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tabel Penempatan Siswa PKL
        Schema::create('pkl_penempatan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('siswa_id', 36);
            $table->uuid('dudi_id');
            $table->char('guru_pembimbing_id', 36); // FK ke users (guru pembimbing sekolah)
            $table->string('tahun_ajaran', 20)->default('2025/2026');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('nama_pembimbing_dudi', 100)->nullable();
            $table->string('nomor_wa_dudi', 25);
            $table->string('token_magic_link_dudi', 64)->unique(); // Token akses DUDI tanpa password
            $table->enum('status', ['draft', 'aktif', 'selesai', 'ditarik'])->default('aktif');
            $table->text('catatan_penempatan')->nullable();
            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('dudi_id')->references('id')->on('pkl_dudi')->onDelete('cascade');
            $table->foreign('guru_pembimbing_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. Tabel Presensi Geolocation Harian Siswa di DUDI
        Schema::create('pkl_presensi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('penempatan_id');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->decimal('lat_masuk', 10, 8)->nullable();
            $table->decimal('long_masuk', 11, 8)->nullable();
            $table->unsignedInteger('jarak_masuk_meter')->nullable();
            $table->string('foto_masuk', 255)->nullable();
            $table->decimal('lat_pulang', 10, 8)->nullable();
            $table->decimal('long_pulang', 11, 8)->nullable();
            $table->unsignedInteger('jarak_pulang_meter')->nullable();
            $table->string('foto_pulang', 255)->nullable();
            $table->enum('status_kehadiran', ['hadir', 'terlambat', 'izin', 'sakit', 'alpa'])->default('hadir');
            $table->boolean('is_in_radius')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('penempatan_id')->references('id')->on('pkl_penempatan')->onDelete('cascade');
            $table->unique(['penempatan_id', 'tanggal']);
        });

        // 4. Tabel Jurnal Aktivitas Harian Siswa PKL (11 Elemen CP PPLG)
        Schema::create('pkl_jurnal_harian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('penempatan_id');
            $table->uuid('presensi_id')->nullable();
            $table->date('tanggal');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->text('ringkasan_pekerjaan');
            $table->text('alat_dan_bahan')->nullable();
            $table->string('elemen_cp', 100); // 11 Elemen Capaian Pembelajaran PPLG
            $table->string('foto_dokumentasi', 255)->nullable();
            
            // Verifikasi / Paraf DUDI (via Magic Link WA)
            $table->enum('paraf_dudi_status', ['pending', 'disetujui', 'perlu_perbaikan'])->default('pending');
            $table->text('catatan_dudi')->nullable();
            $table->dateTime('paraf_dudi_at')->nullable();

            // Verifikasi / Paraf Guru Pembimbing Sekolah
            $table->enum('paraf_guru_status', ['pending', 'disetujui', 'catatan'])->default('pending');
            $table->text('catatan_guru')->nullable();
            $table->dateTime('paraf_guru_at')->nullable();

            $table->timestamps();

            $table->foreign('penempatan_id')->references('id')->on('pkl_penempatan')->onDelete('cascade');
            $table->foreign('presensi_id')->references('id')->on('pkl_presensi')->onDelete('set null');
        });

        // 5. Tabel Asesmen Akhir PKL (Rumus Bobot 5:3:2 SMKN 2 Indramayu)
        Schema::create('pkl_penilaian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('penempatan_id')->unique();
            
            // A. Aspek Soft Skills DUDI (Skala 0-100)
            $table->decimal('nilai_soft_integritas', 5, 2)->default(0);
            $table->decimal('nilai_soft_etos_kerja', 5, 2)->default(0);
            $table->decimal('nilai_soft_gotong_royong', 5, 2)->default(0);
            $table->decimal('nilai_soft_kemandirian', 5, 2)->default(0);
            $table->decimal('nilai_soft_disiplin', 5, 2)->default(0);

            // B. Aspek Hard Skills Teknis PPLG DUDI (Skala 0-100)
            $table->decimal('nilai_hard_tp1', 5, 2)->default(0); // Memahami alur bisnis / sistem
            $table->decimal('nilai_hard_tp2', 5, 2)->default(0); // Penerapan K3LH & Standar Industri
            $table->decimal('nilai_hard_tp3', 5, 2)->default(0); // Desain & Pemrograman / Rekayasa
            $table->decimal('nilai_hard_tp4', 5, 2)->default(0); // Pengujian, Deploy & Dokumentasi

            // Nilai Rekap DUDI (Rata-rata Soft & Hard Skill DUDI) - Bobot 50% (5)
            $table->decimal('nilai_total_dudi', 5, 2)->default(0);
            $table->text('catatan_dudi')->nullable();
            $table->dateTime('dinilai_dudi_at')->nullable();

            // C. Nilai Ujian Sidang Sekolah oleh Guru Penguji - Bobot 30% (3)
            $table->decimal('nilai_sidang_sekolah', 5, 2)->default(0);
            $table->char('penguji_sekolah_id', 36)->nullable();
            $table->text('catatan_penguji')->nullable();

            // D. Nilai Portofolio & Laporan PKL - Bobot 20% (2)
            $table->decimal('nilai_laporan_pkl', 5, 2)->default(0);

            // E. Nilai Akhir (NA = (5*DUDI + 3*Penguji + 2*Laporan) / 10)
            $table->decimal('nilai_akhir_angka', 5, 2)->default(0);
            $table->string('predikat_huruf', 5)->default('E'); // A / B / C / D / E
            $table->enum('status_kelulusan', ['belum_selesai', 'lulus', 'tidak_lulus'])->default('belum_selesai');

            $table->timestamps();

            $table->foreign('penempatan_id')->references('id')->on('pkl_penempatan')->onDelete('cascade');
            $table->foreign('penguji_sekolah_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_penilaian');
        Schema::dropIfExists('pkl_jurnal_harian');
        Schema::dropIfExists('pkl_presensi');
        Schema::dropIfExists('pkl_penempatan');
        Schema::dropIfExists('pkl_dudi');
    }
};