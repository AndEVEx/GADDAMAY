<div>
    <div class="page-header mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1><i class="bi bi-qr-code-scan me-2"></i>Verifikasi Token</h1>
                <p class="subtitle mb-0">Masukkan kode OTP dari guru pengajar</p>
            </div>
            @if($studentRombel)
            <div class="badge bg-primary px-3 py-2 fw-bold" style="font-size: 0.85rem; border-radius: 8px;">
                <i class="bi bi-door-open me-1"></i>Kelas {{ $studentRombel->nama_kelas }}
            </div>
            @endif
        </div>
    </div>

    @if(!$studentRombel)
    <div class="alert alert-warning animate-fade-in-up mb-3" style="border-radius: 12px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Perhatian:</strong> Akun Anda belum terhubung dengan data kelas/rombel di sistem. Silakan hubungi Administrator untuk mengatur kelas pada akun Anda di menu Manajemen User.
    </div>
    @endif

    @if($agenda && in_array($agenda->status, ['token_terverifikasi', 'berjalan']))
    {{-- Status Card for Already Verified Agenda --}}
    <div class="card mb-3 animate-fade-in-up border-success">
        <div class="card-body text-center py-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width: 72px; height: 72px;">
                <i class="bi bi-shield-check text-success" style="font-size: 2.2rem;"></i>
            </div>
            <h5 class="fw-bold text-dark">Token OTP Terverifikasi!</h5>
            <p class="text-muted small mb-3">
                {{ $agenda->guru?->name }} &bull; {{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }}
                <br>Kelas: {{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}
            </p>
            <a href="{{ route('ketua.foto', $agenda->id) }}" class="btn btn-success btn-lg w-100 fw-bold shadow-sm py-3" style="border-radius: 12px;" wire:navigate>
                <i class="bi bi-camera-fill me-2"></i>Buka Kamera Ambil Foto Bukti
            </a>
            <button type="button" wire:click="$set('agenda', null)" class="btn btn-link btn-sm text-muted mt-2 text-decoration-none">
                <i class="bi bi-arrow-repeat me-1"></i>Verifikasi Token Baru
            </button>
        </div>
    </div>
    @else
    {{-- Token Input Form --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-body py-4">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-shield-lock-fill text-primary" style="font-size: 2rem;"></i>
                </div>
                <h5 class="fw-bold">Masukkan Token OTP</h5>
                <p class="text-muted small">Minta kode 6 digit dari guru pengajar di depan kelas</p>
            </div>

            <form wire:submit="verifikasi">
                <div class="mb-3">
                    <input type="text" class="form-control text-center fw-bold @error('token') is-invalid @enderror"
                           wire:model="token" maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                           placeholder="______" style="font-size: 2.2rem; letter-spacing: 0.5rem; padding: 1rem; border-radius: 12px;">
                    @error('token')
                        <div class="invalid-feedback text-center">{{ $message }}</div>
                    @enderror
                </div>

                @if($errorMessage)
                    <div class="alert alert-danger small py-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $errorMessage }}
                    </div>
                @endif

                <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm py-3" style="border-radius: 12px;" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="bi bi-check-circle me-2"></i>Verifikasi Token</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...</span>
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Tabel Mata Pelajaran Hari Ini --}}
    @if($studentRombel)
    <div class="card border-0 shadow-sm animate-fade-in-up mt-3" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-calendar-check text-primary me-2"></i>Mata Pelajaran Hari Ini ({{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }})
                </h6>
                <small class="text-muted">Kelas {{ $studentRombel->nama_kelas }} &bull; Total {{ count($jadwalHariIni) }} sesi</small>
            </div>
            <a href="{{ route('ketua.jadwal') }}" class="btn btn-sm btn-outline-primary fw-semibold px-3 py-1" style="border-radius: 8px;" wire:navigate>
                <i class="bi bi-calendar3 me-1"></i>Lihat Jadwal Mingguan
            </a>
        </div>

        @if($jadwalHariIni->isEmpty())
            <div class="card-body text-center py-4 text-muted">
                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-muted"></i>
                <p class="mb-0 fw-semibold">Tidak ada jadwal mata pelajaran untuk kelas Anda hari ini.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th style="width: 100px;">Jam Ke</th>
                            <th style="width: 130px;">Waktu</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru Pengajar</th>
                            <th class="text-center" style="width: 170px;">Status KBM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalHariIni as $index => $item)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2 py-1" style="border-radius: 6px;">
                                        {{ $item->jam_display }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small fw-semibold">
                                        <i class="bi bi-clock me-1"></i>{{ $item->waktu_display }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->mapel_nama }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        <i class="bi bi-person me-1 text-primary"></i>{{ $item->guru_nama }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $item->status_badge }} px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.76rem; border-radius: 6px;">
                                        <i class="bi {{ $item->status_icon }}"></i>
                                        <span>{{ $item->status_label }}</span>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endif
</div>
