<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Breadcrumb & Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1">
                <span>GADDAMAY VOKASI</span>
                <span>&bull;</span>
                <span>Fase 2: PKL SMKN 2 Indramayu</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Presensi Geolocation PKL</h1>
            <p class="text-sm text-slate-500 mt-1">Presensi digital mandiri berbasis koordinat GPS & radius verifikasi DUDI mitra.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pkl.siswa.jurnal') }}" class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl font-bold text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Buka Jurnal Kegiatan</span>
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if(!$penempatan)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
            <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="font-bold text-amber-900 text-lg">Belum Terdaftar di Penempatan PKL</h3>
            <p class="text-sm text-amber-700 mt-1 max-w-md mx-auto">Akun siswa Anda belum memiliki penempatan aktif di DUDI. Silakan hubungi Koordinator Pokja PKL SMKN 2 Indramayu untuk verifikasi data penempatan.</p>
        </div>
    @else
        <!-- Kartu Info DUDI & Penempatan -->
        <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-8 relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-sky-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                <div>
                    <span class="px-3 py-1 bg-sky-500/20 text-sky-300 border border-sky-400/30 rounded-full text-xs font-bold tracking-wide uppercase">
                        Tempat PKL Resmi SMKN 2 Indramayu
                    </span>
                    <h2 class="text-2xl font-black text-white mt-3">{{ $penempatan->dudi->nama_instansi ?? '-' }}</h2>
                    <p class="text-slate-300 text-sm mt-1 flex items-start gap-1.5">
                        <svg class="w-4 h-4 text-sky-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        <span>{{ $penempatan->dudi->alamat ?? '-' }}</span>
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2 text-xs">
                        <span class="bg-white/10 px-3 py-1 rounded-lg">Pembimbing DUDI: <strong>{{ $penempatan->nama_pembimbing_dudi ?? $penempatan->dudi->pembimbing_nama ?? '-' }}</strong></span>
                        <span class="bg-white/10 px-3 py-1 rounded-lg">WA DUDI: <strong>{{ $penempatan->nomor_wa_dudi }}</strong></span>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/10 flex flex-col justify-between">
                    <div>
                        <div class="text-xs text-slate-300 uppercase font-semibold">Siswa Bimbingan</div>
                        <div class="text-lg font-bold text-white mt-0.5">{{ $penempatan->siswa->nama ?? 'Siswa' }}</div>
                        <div class="text-xs text-slate-300">NISN: {{ $penempatan->siswa->nisn ?? '-' }} | Rombel: {{ $penempatan->siswa->rombel->nama_rombel ?? '-' }}</div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs">
                        <div>
                            <span class="text-slate-400">Guru Pembimbing:</span>
                            <span class="font-bold text-sky-300 ml-1">{{ $penempatan->guruPembimbing->name ?? '-' }}</span>
                        </div>
                        <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 rounded-full font-bold uppercase text-[10px]">
                            Status: {{ strtoupper($penempatan->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Utama Presensi Geolocation -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Box GPS & Status Jarak (2 Kolom) -->
            <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Presensi Hari Ini ({{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }})</span>
                    </h3>
                    <button type="button" onclick="detectGPS()" class="text-xs text-sky-600 hover:text-sky-700 font-bold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span>Refresh GPS</span>
                    </button>
                </div>

                <!-- Banner Status GPS -->
                <div id="gps-banner" class="mb-6 p-4 rounded-2xl transition-all {{ $isInRadius ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-amber-50 border border-amber-200 text-amber-900' }}">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $isInRadius ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-sm">
                                    {{ $isInRadius ? 'Lokasi Valid (Di Lingkungan DUDI)' : 'Status Lokasi Terdeteksi' }}
                                </h4>
                                @if($jarakMeter !== null)
                                    <span class="text-xs font-extrabold px-2.5 py-0.5 rounded-full {{ $isInRadius ? 'bg-emerald-200/80 text-emerald-900' : 'bg-amber-200/80 text-amber-900' }}">
                                        Jarak: {{ $jarakMeter }} meter
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs mt-1 text-slate-600" id="gps-desc">
                                @if($jarakMeter !== null)
                                    Jarak Anda saat ini berjarak <strong>{{ $jarakMeter }} m</strong> dari titik kantor DUDI (Toleransi batas radius: {{ $penempatan->dudi->radius_meter ?? 100 }} m).
                                @else
                                    Mendeteksi koordinat GPS dari perangkat Anda... Mohon aktifkan Lokasi (GPS).
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Upload Foto & Aksi -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Foto Swafoto (Selfie di Lokasi)</label>
                        <input type="file" wire:model="fotoSelfie" accept="image/*" capture="user" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 border border-slate-200 rounded-xl p-1.5">
                        @if ($fotoSelfie)
                            <div class="mt-2 text-xs text-emerald-600 font-semibold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Foto siap diunggah</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Keterangan / Catatan Tambahan (Opsional)</label>
                        <input type="text" wire:model="keterangan" placeholder="Contoh: Tugas piket pagi kantor / Shift 1" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <!-- Tombol Aksi Masuk & Pulang -->
                    <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button type="button" wire:click="presensiMasuk" wire:loading.attr="disabled"
                            class="w-full py-3.5 px-4 rounded-xl font-black text-sm text-white shadow-lg transition flex items-center justify-center gap-2 {{ $todayPresensi && $todayPresensi->jam_masuk ? 'bg-slate-300 cursor-not-allowed text-slate-500' : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-emerald-500/20' }}"
                            {{ $todayPresensi && $todayPresensi->jam_masuk ? 'disabled' : '' }}>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            <span>{{ $todayPresensi && $todayPresensi->jam_masuk ? 'Sudah Presensi Masuk' : 'Presensi Masuk Sekarang' }}</span>
                        </button>

                        <button type="button" wire:click="presensiPulang" wire:loading.attr="disabled"
                            class="w-full py-3.5 px-4 rounded-xl font-black text-sm text-white shadow-lg transition flex items-center justify-center gap-2 {{ !$todayPresensi || ($todayPresensi && $todayPresensi->jam_pulang) ? 'bg-slate-300 cursor-not-allowed text-slate-500' : 'bg-gradient-to-r from-indigo-600 to-sky-600 hover:from-indigo-700 hover:to-sky-700 shadow-indigo-500/20' }}"
                            {{ !$todayPresensi || ($todayPresensi && $todayPresensi->jam_pulang) ? 'disabled' : '' }}>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span>{{ $todayPresensi && $todayPresensi->jam_pulang ? 'Sudah Presensi Pulang' : 'Presensi Pulang' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Box Status Jam Hari Ini -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider text-slate-400 mb-4">Pencatatan Jam Hari Ini</h3>
                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <div class="text-xs font-bold text-slate-500">JAM MASUK</div>
                            <div class="text-2xl font-black text-slate-800 mt-1">
                                {{ $todayPresensi && $todayPresensi->jam_masuk ? substr($todayPresensi->jam_masuk, 0, 5) . ' WIB' : '-- : --' }}
                            </div>
                            @if($todayPresensi && $todayPresensi->jam_masuk)
                                <div class="text-[11px] text-emerald-600 font-semibold mt-1">✓ Berhasil tercatat di sistem</div>
                            @endif
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <div class="text-xs font-bold text-slate-500">JAM PULANG</div>
                            <div class="text-2xl font-black text-slate-800 mt-1">
                                {{ $todayPresensi && $todayPresensi->jam_pulang ? substr($todayPresensi->jam_pulang, 0, 5) . ' WIB' : '-- : --' }}
                            </div>
                            @if($todayPresensi && $todayPresensi->jam_pulang)
                                <div class="text-[11px] text-sky-600 font-semibold mt-1">✓ Selesai jam kerja harian</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <a href="{{ route('pkl.siswa.jurnal') }}" class="w-full py-3 px-4 bg-slate-900 hover:bg-black text-white rounded-xl font-bold text-xs text-center transition block">
                        📝 Tulis Jurnal Kegiatan Harian
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Presensi Terakhir -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <h3 class="font-bold text-slate-900 text-lg mb-4">Riwayat Kehadiran di DUDI</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs text-slate-400 uppercase font-semibold">
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3">Masuk</th>
                            <th class="pb-3">Pulang</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3">Jarak</th>
                            <th class="pb-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($riwayatPresensi as $p)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 font-bold text-slate-800">{{ \Carbon\Carbon::parse($p->tanggal)->isoFormat('dddd, D MMM Y') }}</td>
                                <td class="py-3.5 font-mono text-emerald-700 font-semibold">{{ $p->jam_masuk ? substr($p->jam_masuk, 0, 5) : '-' }}</td>
                                <td class="py-3.5 font-mono text-indigo-700 font-semibold">{{ $p->jam_pulang ? substr($p->jam_pulang, 0, 5) : '-' }}</td>
                                <td class="py-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ $p->status_kehadiran === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $p->status_kehadiran }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-xs text-slate-600">
                                    {{ $p->jarak_masuk_meter !== null ? $p->jarak_masuk_meter . ' m' : '-' }}
                                    @if($p->is_in_radius)
                                        <span class="text-emerald-600 font-bold ml-1">✓ Sah</span>
                                    @else
                                        <span class="text-rose-600 font-bold ml-1">⚠ Luar</span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-xs text-slate-500">{{ $p->keterangan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 text-sm">Belum ada riwayat presensi yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<script>
    function detectGPS() {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung deteksi lokasi Geolocation.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                let lat = position.coords.latitude;
                let lng = position.coords.longitude;
                @this.call('updateCoordinates', lat, lng);
            },
            function(error) {
                console.warn('GPS Error: ' + error.message);
                @this.call('setGpsError', error.message);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    document.addEventListener('DOMContentLoaded', function() {
        detectGPS();
    });
</script>