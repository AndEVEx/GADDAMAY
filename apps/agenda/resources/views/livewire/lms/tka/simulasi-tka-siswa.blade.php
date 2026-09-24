<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-zinc-800 p-6 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-700 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-full text-xs font-semibold mb-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                LMS Pusat Latihan TKA Vokasi
            </div>
            <h1 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Simulasi Tes Kemampuan Akademik (TKA)</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                Asesmen kemampuan skolastik, penalaran logika, dan numerasi. Hasil dapat dipantau langsung oleh Anda dan Wali Kelas.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('lms.siswa.dashboard') }}" class="px-4 py-2 text-sm font-medium text-zinc-600 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 rounded-xl transition">
                Kembali ke LMS Siswa
            </a>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Mode 1: Daftar Paket & Riwayat -->
    @if($mode === 'daftar')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Paket Soal Tersedia -->
        <div class="lg:col-span-2 space-y-4">
            <h2 class="text-lg font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Pilih Paket Latihan TKA
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($paketList as $p)
                    <div class="bg-white dark:bg-zinc-800 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm flex flex-col justify-between hover:border-indigo-500 transition">
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 mb-2">
                                {{ $p->mata_uji }}
                            </span>
                            <h3 class="font-bold text-base text-zinc-900 dark:text-white">{{ $p->judul_paket }}</h3>
                            <p class="text-xs text-zinc-500 mt-1">{{ Str::limit($p->deskripsi ?: 'Simulasi latihan soal TKA standar vokasi.', 80) }}</p>
                            
                            <div class="flex items-center gap-4 text-xs text-zinc-600 dark:text-zinc-400 mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-700">
                                <span>⏱️ <strong>{{ $p->durasi_menit }}</strong> Menit</span>
                                <span>📝 <strong>{{ $p->soal_count }}</strong> Butir Soal</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-2">
                            <button wire:click="mulaiUjian('{{ $p->id }}')" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                Mulai Simulasi Sekarang
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-8 text-zinc-400 bg-white dark:bg-zinc-800 rounded-2xl border">
                        Belum ada paket latihan TKA aktif saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Riwayat Nilai Siswa (Dapat dilihat Wali Kelas) -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Riwayat Hasil Tes Anda
            </h2>

            <div class="bg-white dark:bg-zinc-800 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm space-y-3">
                @forelse($riwayatHasil as $h)
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-100 dark:border-zinc-750">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-xs text-zinc-900 dark:text-white">{{ $h->paket?->judul_paket }}</h4>
                                <p class="text-[10px] text-zinc-400">{{ $h->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <span class="text-base font-black {{ $h->nilai_skor >= 75 ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $h->nilai_skor }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-[11px] text-zinc-500 mt-2">
                            <span>✅ Benar: <strong>{{ $h->jumlah_benar }}</strong></span>
                            <span>❌ Salah: <strong>{{ $h->jumlah_salah }}</strong></span>
                            <span>⏱️ {{ gmdate("i:s", $h->durasi_detik) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-zinc-400 italic text-center py-4">Belum ada riwayat pengerjaan tes.</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    <!-- Mode 2: Sesi Pengerjaan Soal (CBT Interface) -->
    @if($mode === 'ujian' && $activePaket)
    @php
        $currentSoal = $activePaket->soal[$currentSoalIndex] ?? null;
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Lembar Soal -->
        <div class="lg:col-span-3 bg-white dark:bg-zinc-800 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm space-y-6">
            <div class="flex justify-between items-center border-b border-zinc-200 dark:border-zinc-700 pb-4">
                <div>
                    <span class="text-xs font-bold text-indigo-600 uppercase">Soal Nomor {{ $currentSoalIndex + 1 }} dari {{ $activePaket->soal->count() }}</span>
                    <h3 class="font-bold text-zinc-900 dark:text-white text-sm">{{ $activePaket->judul_paket }}</h3>
                </div>
                <div class="px-3 py-1.5 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 rounded-xl text-xs font-mono font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Durasi: {{ $activePaket->durasi_menit }} Menit
                </div>
            </div>

            @if($currentSoal)
            <div class="space-y-4">
                <div class="text-base text-zinc-900 dark:text-white font-medium leading-relaxed">
                    {!! nl2br(e($currentSoal->pertanyaan)) !!}
                </div>

                <!-- Pilihan Jawaban A, B, C, D, E -->
                <div class="space-y-2 pt-2">
                    @foreach(['A' => $currentSoal->pilihan_a, 'B' => $currentSoal->pilihan_b, 'C' => $currentSoal->pilihan_c, 'D' => $currentSoal->pilihan_d] as $opsi => $teks)
                        @php
                            $isSelected = ($jawabanSiswa[$currentSoal->id] ?? null) === $opsi;
                        @endphp
                        <button type="button" wire:click="pilihJawaban('{{ $currentSoal->id }}', '{{ $opsi }}')" class="w-full text-left p-3.5 rounded-xl border transition flex items-start gap-3 {{ $isSelected ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200' : 'border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-750' }}">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $isSelected ? 'bg-indigo-600 text-white' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300' }}">{{ $opsi }}</span>
                            <span class="text-sm pt-0.5">{{ $teks }}</span>
                        </button>
                    @endforeach

                    @if($currentSoal->pilihan_e && $currentSoal->pilihan_e !== '-')
                        @php
                            $isSelectedE = ($jawabanSiswa[$currentSoal->id] ?? null) === 'E';
                        @endphp
                        <button type="button" wire:click="pilihJawaban('{{ $currentSoal->id }}', 'E')" class="w-full text-left p-3.5 rounded-xl border transition flex items-start gap-3 {{ $isSelectedE ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200' : 'border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-750' }}">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $isSelectedE ? 'bg-indigo-600 text-white' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300' }}">E</span>
                            <span class="text-sm pt-0.5">{{ $currentSoal->pilihan_e }}</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Navigasi Bawah -->
            <div class="flex justify-between items-center pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <button type="button" wire:click="prevSoal" {{ $currentSoalIndex == 0 ? 'disabled' : '' }} class="px-4 py-2 bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 rounded-xl text-xs font-bold disabled:opacity-50">
                    &larr; Soal Sebelumnya
                </button>
                <button type="button" wire:click="nextSoal" {{ $currentSoalIndex >= $activePaket->soal->count() - 1 ? 'disabled' : '' }} class="px-4 py-2 bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 rounded-xl text-xs font-bold disabled:opacity-50">
                    Soal Berikutnya &rarr;
                </button>
            </div>
            @endif
        </div>

        <!-- Peta Navigasi Butir Soal -->
        <div class="space-y-4">
            <div class="bg-white dark:bg-zinc-800 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm space-y-4">
                <h4 class="font-bold text-xs uppercase tracking-wider text-zinc-500">Navigasi Soal</h4>
                <div class="grid grid-cols-5 gap-2">
                    @foreach($activePaket->soal as $idx => $sl)
                        @php
                            $isAnswered = isset($jawabanSiswa[$sl->id]);
                            $isCurrent = $currentSoalIndex == $idx;
                        @endphp
                        <button type="button" wire:click="jumpSoal({{ $idx }})" class="h-9 rounded-lg text-xs font-bold transition flex items-center justify-center border {{ $isCurrent ? 'ring-2 ring-indigo-500 border-indigo-500' : '' }} {{ $isAnswered ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300' }}">
                            {{ $idx + 1 }}
                        </button>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <button type="button" wire:click="selesaikanUjian" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan simulasi TKA ini?')" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                        Selesaikan Ujian Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Mode 3: Hasil Evaluasi Simulasi -->
    @if($mode === 'hasil' && $hasilAkhir)
    <div class="max-w-2xl mx-auto bg-white dark:bg-zinc-800 p-8 rounded-3xl border border-zinc-200 dark:border-zinc-700 shadow-xl text-center space-y-6">
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>

        <div>
            <span class="text-xs font-bold text-emerald-600 uppercase">Simulasi Selesai</span>
            <h2 class="text-2xl font-black text-zinc-900 dark:text-white mt-1">{{ $hasilAkhir->paket?->judul_paket }}</h2>
            <p class="text-xs text-zinc-400">Data hasil tes otomatis tersimpan dan dapat ditinjau oleh Wali Kelas Anda.</p>
        </div>

        <div class="p-6 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700">
            <span class="text-xs font-semibold text-zinc-400 uppercase">Skor Nilai TKA Akhir</span>
            <div class="text-5xl font-black text-indigo-600 mt-1">{{ $hasilAkhir->nilai_skor }}</div>
            <div class="flex justify-center gap-6 mt-4 text-xs font-semibold text-zinc-600 dark:text-zinc-300">
                <span class="text-emerald-600">✅ Benar: {{ $hasilAkhir->jumlah_benar }}</span>
                <span class="text-rose-600">❌ Salah: {{ $hasilAkhir->jumlah_salah }}</span>
                <span>⏱️ Durasi: {{ gmdate("i:s", $hasilAkhir->durasi_detik) }}</span>
            </div>
        </div>

        <button wire:click="kembaliKeDaftar" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow transition">
            Kembali ke Daftar Paket
        </button>
    </div>
    @endif
</div>
