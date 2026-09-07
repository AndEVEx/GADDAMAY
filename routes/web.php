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
use App\Livewire\Waka\VerifikasiIzin;

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

Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'kepsek', 'waka' => redirect()->route('monitoring.dashboard'),
        'ketua_mgmp', 'guru' => redirect()->route('guru.dashboard'),
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
