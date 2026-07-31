<?php

use Illuminate\Support\Facades\Route;
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
use App\Livewire\Admin\KoreksiAgenda;
use App\Livewire\Admin\OverrideAgenda;
use App\Livewire\Monitoring\DashboardMonitoring;
use App\Livewire\Monitoring\ProgressTp;
use App\Livewire\Admin\ImportKktp;

// ============================================================
// PUBLIC ROUTES
// ============================================================
Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

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
});

// ============================================================
// GURU ROUTES
// ============================================================
Route::middleware(['auth', 'role:guru,ketua_mgmp'])->prefix('guru')->group(function () {
    Route::get('/dashboard', DashboardGuru::class)->name('guru.dashboard');
    Route::get('/jurnal', JurnalTahunan::class)->name('guru.jurnal');
    Route::get('/jurnal/{rombel}', JurnalPerKelas::class)->name('guru.jurnal-kelas');

    // These require time restriction
    Route::middleware('time-restriction')->group(function () {
        Route::get('/mulai/{jadwal}', MulaiKelas::class)->name('guru.mulai');
        Route::get('/materi/{agenda}', IsiMateri::class)->name('guru.materi');
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
    Route::get('/koreksi', KoreksiAgenda::class)->name('admin.koreksi');
    Route::get('/override/{agenda}', OverrideAgenda::class)->name('admin.override');
});

// ============================================================
// MONITORING ROUTES (Kepsek + Waka + Admin)
// ============================================================
Route::middleware(['auth', 'role:admin,kepsek,waka'])->prefix('monitoring')->group(function () {
    Route::get('/dashboard', DashboardMonitoring::class)->name('monitoring.dashboard');
    Route::get('/progress-tp', ProgressTp::class)->name('monitoring.progress');
});

// ============================================================
// WAKA ROUTES (Koreksi)
// ============================================================
Route::middleware(['auth', 'role:waka'])->prefix('waka')->group(function () {
    Route::get('/koreksi', KoreksiAgenda::class)->name('waka.koreksi');
    Route::get('/override/{agenda}', OverrideAgenda::class)->name('waka.override');
});
