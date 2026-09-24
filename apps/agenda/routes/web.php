<?php

use Illuminate\Support\Facades\Route;
use Livewire\Mechanisms\HandleRequests\EndpointResolver;
use App\Http\Controllers\LivewireCustomFileUploadController;
use App\Http\Controllers\DirectImportController;
use App\Http\Controllers\Api\GuruScheduleController;
use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\Guru\JurnalExportController;
use App\Livewire\Auth\Login;
use App\Livewire\Guru\DashboardGuru;
use App\Livewire\Guru\MulaiKelas;
use App\Livewire\Guru\IsiMateri;
use App\Livewire\Guru\Stopwatch;
use App\Livewire\Guru\InputKehadiran;
use App\Livewire\Guru\InputKktp;
use App\Livewire\Guru\JurnalTahunan;
use App\Livewire\Guru\JurnalPerKelas;
use App\Livewire\KetuaKelas\VerifikasiToken;
use App\Livewire\KetuaKelas\AmbilFoto;
use App\Livewire\KetuaKelas\AnggotaKelas;
use App\Livewire\KetuaKelas\JadwalKelas;
use App\Livewire\KetuaMgmp\DashboardKetuaMgmp;
use App\Livewire\KetuaMgmp\ManajemenTP;
use App\Livewire\Admin\DashboardAdmin;
use App\Livewire\Admin\ImportJadwal;
use App\Livewire\Admin\ImportSiswa;
use App\Livewire\Admin\ManajemenJadwal;
use App\Livewire\Admin\ManajemenGuru;
use App\Livewire\Admin\ManajemenKelas;
use App\Livewire\Admin\ManajemenMapel;
use App\Livewire\Admin\ManajemenSiswa;
use App\Livewire\Admin\ManajemenUser;
use App\Livewire\Admin\ManajemenMotivasiPantun;
use App\Livewire\Admin\ManajemenHariLibur;
use App\Livewire\Admin\KoreksiAgenda;
use App\Livewire\Admin\OverrideAgenda;
use App\Livewire\Admin\ImportKktp;
use App\Livewire\Monitoring\DashboardMonitoring;
use App\Livewire\Monitoring\MonitoringHarian;
use App\Livewire\Monitoring\MonitoringMingguan;
use App\Livewire\Monitoring\ProgressTp;
use App\Livewire\Guru\FotoGuru;
use App\Livewire\Guru\DetailAgenda;
use App\Livewire\Guru\KktpHub;
use App\Livewire\Guru\ImportKktpGuru;
use App\Livewire\Guru\SettingKktp;
use App\Livewire\Guru\NilaiKktpIndex;
use App\Livewire\Guru\NilaiKktpDetail;
use App\Livewire\Guru\PengajuanIzin;
use App\Livewire\Guru\Performa\JadwalMingguan;
use App\Livewire\Guru\Performa\SiswaDiajar;
use App\Livewire\Guru\Performa\KehadiranBulanan;
use App\Livewire\Guru\Performa\ExportIki;
use App\Livewire\Guru\RekapAbsensiSiswa;
use App\Livewire\Admin\LiveAbsensiSiswa;
use App\Livewire\Admin\RekapAbsensiAdmin;
use App\Http\Controllers\Guru\IkiExportController;
use App\Livewire\Waka\VerifikasiIzin;
use App\Livewire\Perizinan\ManajemenPerizinanSiswa;
use App\Livewire\Perizinan\PengajuanIzinSiswa;
use App\Livewire\Portal\PortalUtama;
use App\Livewire\Pkl\Siswa\PresensiPkl;
use App\Livewire\Pkl\Siswa\JurnalPkl;
use App\Livewire\Pkl\Dudi\ReviewDudi;
use App\Livewire\Pkl\Guru\MonitoringPkl;
use App\Livewire\Pkl\Admin\KelolaPenempatanPkl;
use App\Livewire\Parenting\LoginParenting;
use App\Livewire\Parenting\DashboardParenting;
use App\Livewire\Parenting\Guru\KelolaDisiplinSiswa;
use App\Livewire\Tefa\Admin\ManajemenOrderTefa;
use App\Livewire\Tefa\Admin\DetailOrderTefa;
use App\Livewire\Tefa\Siswa\LogProduksiTefa;
use App\Livewire\Lms\Guru\KelolaMateriLms;
use App\Livewire\Lms\Siswa\MyLearningDashboard;
use App\Livewire\Ujikom\Siswa\PendaftaranUjikomSiswa;
use App\Livewire\Ujikom\Admin\VerifikasiPendaftaranUjikom;
use App\Livewire\Admin\ManajemenTugasTambahanGuru;
use App\Livewire\Lms\Fisik\KelolaTesFisikSiswa;
use App\Livewire\Lms\Tka\KelolaLatihanTka;
use App\Livewire\Lms\Tka\SimulasiTkaSiswa;
use App\Livewire\Guru\WaliKelas\MonitoringWaliKelas;

// ============================================================
// LIVEWIRE FILE UPLOAD OVERRIDE ROUTES (Catch all livewire upload paths)
// ============================================================
Route::post(EndpointResolver::uploadPath(), [LivewireCustomFileUploadController::class, 'handle'])
    ->name('livewire.upload-file');
Route::post('/{livewire_path}/upload-file', [LivewireCustomFileUploadController::class, 'handle'])
    ->where('livewire_path', 'livewire.*');

use App\Livewire\Auth\GantiPassword;

// ============================================================
// PUBLIC ROUTES
// ============================================================
Route::get('/', PortalUtama::class)->name('portal.home');
Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', function () {
    $userId = auth()->id();
    if ($userId) {
        \App\Models\AuditLog::create([
            'user_id' => $userId,
            'action' => 'logout',
            'auditable_type' => \App\Models\User::class,
            'auditable_id' => $userId,
            'new_values' => [
                'name' => auth()->user()?->name,
                'role' => auth()->user()?->role,
                'logged_out_at' => \Carbon\Carbon::now('Asia/Jakarta')->toDateTimeString(),
            ],
            'ip_address' => request()->ip(),
        ]);
        \Illuminate\Support\Facades\Cache::forget('user_online_' . $userId);
    }
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

Route::get('/ganti-password', GantiPassword::class)->middleware('auth')->name('ganti-password');

// API: Guru schedule for push notification
Route::get('/api/guru/jadwal-hari-ini', [GuruScheduleController::class, 'todaySchedule'])
    ->middleware(['auth'])
    ->name('api.guru.jadwal');

// API: Web Push Subscription Endpoints
Route::get('/api/push/vapid-public-key', [PushSubscriptionController::class, 'getVapidPublicKey'])
    ->name('api.push.vapid-key');
Route::post('/api/push/subscribe', [PushSubscriptionController::class, 'subscribe'])
    ->middleware(['auth'])
    ->name('api.push.subscribe');
Route::post('/api/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])
    ->middleware(['auth'])
    ->name('api.push.unsubscribe');
Route::post('/api/push/test-notification', [PushSubscriptionController::class, 'sendTestPush'])
    ->middleware(['auth'])
    ->name('api.push.test');

Route::middleware('auth')->get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'kepsek', 'waka' => redirect()->route('monitoring.dashboard'),
        'ketua_mgmp', 'guru' => redirect()->route('guru.dashboard'),
        'guru_piket' => redirect()->route('perizinan.index'),
        'ketua_kelas' => redirect()->route('ketua.verifikasi'),
        default => redirect()->route('login'),
    };
})->name('home');

// ============================================================
// KETUA KELAS ROUTES
// ============================================================
Route::middleware(['auth', 'role:ketua_kelas'])->prefix('ketua-kelas')->group(function () {
    Route::get('/verifikasi', VerifikasiToken::class)->name('ketua.verifikasi');
    Route::get('/foto/{agenda}', AmbilFoto::class)->name('ketua.foto');
    Route::get('/jadwal', JadwalKelas::class)->name('ketua.jadwal');
    Route::get('/anggota-kelas', AnggotaKelas::class)->name('ketua.anggota');
});

// ============================================================
// GURU ROUTES
// ============================================================
Route::middleware(['auth', 'role:guru,ketua_mgmp'])->prefix('guru')->group(function () {
    Route::get('/dashboard', DashboardGuru::class)->name('guru.dashboard');
    Route::get('/jurnal', JurnalTahunan::class)->name('guru.jurnal');
    Route::get('/jurnal/{rombel}', JurnalPerKelas::class)->name('guru.jurnal-kelas');
    Route::get('/jurnal/{rombel}/export-pdf', [JurnalExportController::class, 'exportPdf'])->name('guru.jurnal.export-pdf');
    Route::get('/detail/{agenda}', DetailAgenda::class)->name('guru.detail-agenda')->withTrashed();

    // KKTP Menu (Setting, Nilai, Import)
    Route::get('/menu-kktp', KktpHub::class)->name('guru.kktp-hub');
    Route::get('/menu-kktp/import', ImportKktpGuru::class)->name('guru.import-kktp');
    Route::get('/menu-kktp/setting', SettingKktp::class)->name('guru.kktp-setting');
    Route::get('/menu-kktp/nilai', NilaiKktpIndex::class)->name('guru.kktp-nilai');
    Route::get('/menu-kktp/nilai/{rombel}/{mapel}', NilaiKktpDetail::class)->name('guru.kktp-nilai-detail');

    // Pengajuan Izin Guru
    Route::get('/izin', PengajuanIzin::class)->name('guru.izin');

    // Rekap Absensi Siswa Matrix
    Route::get('/rekap-absensi/{rombel?}/{mapel?}', RekapAbsensiSiswa::class)->name('guru.rekap-absensi');

    // Kelompok Menu "Performa Saya"
    Route::prefix('performa')->group(function () {
        Route::get('/jadwal-mingguan', JadwalMingguan::class)->name('guru.performa.jadwal-mingguan');
        Route::get('/siswa-diajar', SiswaDiajar::class)->name('guru.performa.siswa-diajar');
        Route::get('/kehadiran-bulanan', KehadiranBulanan::class)->name('guru.performa.kehadiran-bulanan');
        Route::get('/export-iki', ExportIki::class)->name('guru.performa.export-iki');
        Route::get('/export-iki/pdf', [IkiExportController::class, 'exportPdf'])->name('guru.performa.export-iki.pdf');
    });

    // These require time restriction
    Route::middleware('time-restriction')->group(function () {
        Route::get('/mulai/{jadwal}', MulaiKelas::class)->name('guru.mulai');
        Route::get('/materi/{agenda}', IsiMateri::class)->name('guru.materi');
        Route::get('/foto-guru/{agenda}', FotoGuru::class)->name('guru.foto-guru');
        Route::get('/stopwatch/{agenda}', Stopwatch::class)->name('guru.stopwatch');
        Route::get('/kehadiran/{agenda}', InputKehadiran::class)->name('guru.kehadiran');
        Route::get('/kktp/{agenda}', InputKktp::class)->name('guru.kktp');
    });
});

// ============================================================
// KETUA MGMP ROUTES
// ============================================================
Route::middleware(['auth', 'role:ketua_mgmp'])->prefix('mgmp')->group(function () {
    Route::get('/dashboard', DashboardKetuaMgmp::class)->name('mgmp.dashboard');
    Route::get('/tp', ManajemenTP::class)->name('mgmp.tp');
});

// ============================================================
// ADMIN ROUTES
// ============================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', DashboardAdmin::class)->name('admin.dashboard');
    Route::get('/import-jadwal', ImportJadwal::class)->name('admin.import');
    Route::get('/import-siswa', ImportSiswa::class)->name('admin.import-siswa');
    Route::get('/import-kktp', ImportKktp::class)->name('admin.import-kktp');
    Route::get('/jadwal', ManajemenJadwal::class)->name('admin.jadwal');
    Route::get('/guru', ManajemenGuru::class)->name('admin.guru');
    Route::get('/kelas', ManajemenKelas::class)->name('admin.kelas');
    Route::get('/mapel', ManajemenMapel::class)->name('admin.mapel');
    Route::get('/siswa', ManajemenSiswa::class)->name('admin.siswa');
    Route::get('/users', ManajemenUser::class)->name('admin.users');
    Route::get('/motivasi', ManajemenMotivasiPantun::class)->name('admin.motivasi');
    Route::get('/hari-libur', ManajemenHariLibur::class)->name('admin.hari-libur');
    Route::get('/koreksi', KoreksiAgenda::class)->name('admin.koreksi');
    Route::get('/override/{agenda}', OverrideAgenda::class)->name('admin.override');
    Route::get('/verifikasi-izin', VerifikasiIzin::class)->name('admin.verifikasi-izin');
    Route::get('/live-absensi', LiveAbsensiSiswa::class)->name('admin.live-absensi');
    Route::get('/rekap-absensi', RekapAbsensiAdmin::class)->name('admin.rekap-absensi');
    Route::get('/tugas-tambahan', ManajemenTugasTambahanGuru::class)->name('admin.tugas-tambahan');

    // Direct Form Upload Fallbacks (Fail-safe HTTP POST routes)
    Route::post('/direct-import-jadwal', [DirectImportController::class, 'importJadwal'])->name('admin.direct-import-jadwal');
    Route::post('/direct-import-siswa', [DirectImportController::class, 'importSiswa'])->name('admin.direct-import-siswa');
    Route::post('/direct-import-kktp', [DirectImportController::class, 'importKktp'])->name('admin.direct-import-kktp');
    Route::post('/direct-import-guru', [DirectImportController::class, 'importGuru'])->name('admin.direct-import-guru');
    Route::post('/direct-import-kelas', [DirectImportController::class, 'importKelas'])->name('admin.direct-import-kelas');
    Route::post('/direct-import-mapel', [DirectImportController::class, 'importMapel'])->name('admin.direct-import-mapel');
    Route::post('/direct-import-user', [DirectImportController::class, 'importUser'])->name('admin.direct-import-user');
    Route::post('/direct-import-hari-libur', [DirectImportController::class, 'importHariLibur'])->name('admin.direct-import-hari-libur');
});

// ============================================================
// MONITORING ROUTES (Kepsek + Waka + Admin)
// ============================================================
Route::middleware(['auth', 'role:admin,kepsek,waka'])->prefix('monitoring')->group(function () {
    Route::get('/dashboard', DashboardMonitoring::class)->name('monitoring.dashboard');
    Route::get('/live-absensi', LiveAbsensiSiswa::class)->name('monitoring.live-absensi');
    Route::get('/rekap-absensi', RekapAbsensiAdmin::class)->name('monitoring.rekap-absensi');
    Route::get('/harian', MonitoringHarian::class)->name('monitoring.harian');
    Route::get('/mingguan', MonitoringMingguan::class)->name('monitoring.mingguan');
    Route::get('/progress-tp', ProgressTp::class)->name('monitoring.progress');
    Route::get('/izin-guru', VerifikasiIzin::class)->name('monitoring.izin-guru');
});

// ============================================================
// WAKA ROUTES (Koreksi)
// ============================================================
Route::middleware(['auth', 'role:waka'])->prefix('waka')->group(function () {
    Route::get('/koreksi', KoreksiAgenda::class)->name('waka.koreksi');
    Route::get('/override/{agenda}', OverrideAgenda::class)->name('waka.override');
    Route::get('/verifikasi-izin', VerifikasiIzin::class)->name('waka.verifikasi-izin');
});

// ============================================================
// PERIZINAN SISWA TERPADU (GADDAMAY)
// ============================================================
Route::middleware('auth')->prefix('perizinan')->group(function () {
    Route::get('/', ManajemenPerizinanSiswa::class)->name('perizinan.index');
    Route::get('/pengajuan', PengajuanIzinSiswa::class)->name('perizinan.pengajuan');
});

// ============================================================
// MODUL JURNAL & ASESMEN PKL VOKASI SMKN 2 INDRAMAYU (FASE 2)
// ============================================================
// 1. Rute Publik Akses DUDI Tanpa Password (Magic Link WA)
Route::get('/pkl/review-dudi/{token}', ReviewDudi::class)->name('pkl.dudi.review');

// 2. Rute Siswa PKL (Presensi GPS & Jurnal Harian)
Route::prefix('pkl')->group(function () {
    Route::get('/presensi', PresensiPkl::class)->name('pkl.siswa.presensi');
    Route::get('/jurnal', JurnalPkl::class)->name('pkl.siswa.jurnal');
    Route::get('/monitoring', MonitoringPkl::class)->name('pkl.guru.monitoring');
    Route::get('/penempatan', KelolaPenempatanPkl::class)->name('pkl.admin.penempatan');
});

// ============================================================
// MODUL BUKU MONITORING PARENTING DIGITAL (FASE 3)
// ============================================================
Route::get('/parenting', LoginParenting::class)->name('parenting.login');
Route::get('/parenting/dashboard/{token?}', DashboardParenting::class)->name('parenting.dashboard');
Route::get('/parenting/disiplin-bk', KelolaDisiplinSiswa::class)->name('parenting.guru.disiplin');

// ============================================================
// MODUL JURNAL TEACHING FACTORY (TEFA) - FASE 4
// ============================================================
Route::prefix('tefa')->group(function () {
    Route::get('/orders', ManajemenOrderTefa::class)->name('tefa.admin.orders');
    Route::get('/orders/{id}', DetailOrderTefa::class)->name('tefa.admin.detail');
    Route::get('/log-produksi', LogProduksiTefa::class)->name('tefa.siswa.log');
});

// ============================================================
// MODUL LMS VOKASI DIFERENSIASI, UJIKOM, LKS, TES FISIK & TKA
// ============================================================
Route::prefix('lms')->group(function () {
    Route::get('/my-learning', MyLearningDashboard::class)->name('lms.siswa.dashboard');
    Route::get('/kelola-materi', KelolaMateriLms::class)->name('lms.guru.materi');

    // 1. Fitur Guru Olahraga: Pendataan Kemampuan Fisik Siswa
    Route::get('/tes-fisik', KelolaTesFisikSiswa::class)->name('lms.fisik.kelola');

    // 2. Fitur Latihan Soal TKA (Bank Soal & CBT Siswa)
    Route::get('/tka/kelola', KelolaLatihanTka::class)->name('lms.tka.kelola');
    Route::get('/tka/simulasi', SimulasiTkaSiswa::class)->name('lms.tka.simulasi');
});

// ============================================================
// MONITORING WALI KELAS (Fisik, TKA & Presensi Siswa Binaan)
// ============================================================
Route::middleware(['auth'])->prefix('wali-kelas')->group(function () {
    Route::get('/monitoring', MonitoringWaliKelas::class)->name('walikelas.monitoring');
});

// ============================================================
// MODUL PENDAFTARAN & ASESMEN UJIKOM LSP-P1
// ============================================================
Route::prefix('ujikom')->group(function () {
    Route::get('/daftar', PendaftaranUjikomSiswa::class)->name('ujikom.daftar');
    Route::get('/verifikasi', VerifikasiPendaftaranUjikom::class)->name('ujikom.admin.verifikasi');
});

