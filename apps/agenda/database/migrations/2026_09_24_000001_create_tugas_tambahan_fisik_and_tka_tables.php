<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modifikasi lms_materi kategori enum menjadi string agar support latihan_tka & kebugaran_jasmani
        Schema::table('lms_materi', function (Blueprint $table) {
            $table->string('kategori', 50)->default('kbm_reguler')->change();
        });

        // 2. Tabel Tugas Tambahan Guru (Wali Kelas, Pembina Kesiswaan, Guru BK, Guru Piket, Koordinator Literasi, dll.)
        Schema::create('tugas_tambahan_guru', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('guru_id', 36);
            $table->string('jenis_tugas', 50); // wali_kelas, pembina_kesiswaan, guru_bk, guru_piket, koordinator_literasi
            $table->char('rombel_id', 36)->nullable(); // untuk wali kelas
            $table->string('tahun_ajaran', 20)->default('2025/2026');
            $table->string('sk_penugasan', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('guru_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rombel_id')->references('id')->on('rombel')->onDelete('set null');
        });

        // 3. Tabel Tes Kebugaran Jasmani / Kemampuan Fisik Siswa (Guru Olahraga / PJOK)
        Schema::create('lms_tes_fisik_siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('siswa_id', 36);
            $table->char('guru_olahraga_id', 36);
            $table->date('tanggal_tes');
            $table->string('semester', 20)->default('Ganjil');
            $table->string('tahun_ajaran', 20)->default('2025/2026');
            
            // Komponen Tes Fisik & Kebugaran Jasmani Vokasi
            $table->decimal('tinggi_badan_cm', 5, 1)->nullable();
            $table->decimal('berat_badan_kg', 5, 1)->nullable();
            $table->decimal('bmi', 4, 1)->nullable();
            $table->string('kategori_bmi', 30)->nullable(); // Kurus, Ideal, Berlebih, Obesitas
            
            $table->integer('lari_1200m_detik')->nullable(); // Daya tahan jantung / cooper test (detik)
            $table->integer('push_up_1min')->nullable(); // Kekuatan otot lengan
            $table->integer('sit_up_1min')->nullable(); // Kekuatan otot perut
            $table->decimal('shuttle_run_detik', 4, 2)->nullable(); // Kelincahan (4x10m)
            $table->decimal('sit_and_reach_cm', 4, 1)->nullable(); // Kelenturan (cm)
            
            $table->decimal('skor_kebugaran', 5, 2)->default(0); // Nilai rata-rata / konversi skor
            $table->string('predikat', 30)->default('Cukup'); // Sangat Baik, Baik, Cukup, Kurang
            $table->text('catatan_guru_olahraga')->nullable();
            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('guru_olahraga_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 4. Tabel Bank Soal & Paket Latihan Tes Kemampuan Akademik (TKA)
        Schema::create('lms_tka_paket', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul_paket', 150);
            $table->string('mata_uji', 100); // TPA Verbal, Logika, Matematika Terapan, Skolastik, Literasi
            $table->char('guru_pembuat_id', 36);
            $table->integer('durasi_menit')->default(60);
            $table->integer('jumlah_soal')->default(20);
            $table->string('target_tingkat', 10)->default('Semua'); // X, XI, XII, Semua
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('guru_pembuat_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Butir Soal TKA
        Schema::create('lms_tka_soal', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('paket_id');
            $table->integer('nomor_urut')->default(1);
            $table->text('pertanyaan');
            $table->string('pilihan_a', 255);
            $table->string('pilihan_b', 255);
            $table->string('pilihan_c', 255);
            $table->string('pilihan_d', 255);
            $table->string('pilihan_e', 255)->nullable();
            $table->char('kunci_jawaban', 1); // A, B, C, D, E
            $table->text('pembahasan')->nullable();
            $table->timestamps();

            $table->foreign('paket_id')->references('id')->on('lms_tka_paket')->onDelete('cascade');
        });

        // Hasil Pengerjaan Latihan TKA oleh Siswa
        Schema::create('lms_tka_hasil_siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('paket_id');
            $table->char('siswa_id', 36);
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai')->nullable();
            $table->integer('durasi_detik')->default(0);
            $table->integer('jumlah_benar')->default(0);
            $table->integer('jumlah_salah')->default(0);
            $table->decimal('nilai_skor', 5, 2)->default(0); // 0 - 100
            $table->json('lembar_jawaban')->nullable(); // detail jawaban tiap soal
            $table->string('status', 20)->default('selesai'); // sedang_mengerjakan, selesai
            $table->timestamps();

            $table->foreign('paket_id')->references('id')->on('lms_tka_paket')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_tka_hasil_siswa');
        Schema::dropIfExists('lms_tka_soal');
        Schema::dropIfExists('lms_tka_paket');
        Schema::dropIfExists('lms_tes_fisik_siswa');
        Schema::dropIfExists('tugas_tambahan_guru');
    }
};