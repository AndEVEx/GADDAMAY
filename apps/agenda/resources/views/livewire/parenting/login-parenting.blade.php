<div class="max-w-xl mx-auto px-4 py-12">
    <!-- Header Card -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-tr from-pink-500 to-rose-600 text-white rounded-3xl shadow-xl shadow-rose-500/20 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <div class="text-xs font-black tracking-widest text-rose-600 uppercase mb-1">GADDAMAY PARENTING</div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Portal Pengawasan Orang Tua</h1>
        <p class="text-sm text-slate-500 mt-2">Akses pantau presensi gerbang, kehadiran KBM kelas, serta buku kedisiplinan putra-putri Anda tanpa perlu kata sandi rumit.</p>
    </div>

    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="mb-6 p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-800 text-xs font-semibold flex items-center gap-3">
            <svg class="w-5 h-5 text-sky-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- Box Form Login -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xl shadow-slate-100 mb-8">
        <!-- Tab Selector -->
        <div class="flex p-1 bg-slate-100 rounded-2xl mb-6">
            <button type="button" wire:click="$set('activeMethod', 'nisn')" class="flex-1 py-2.5 rounded-xl text-xs font-bold transition {{ $activeMethod === 'nisn' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                1. Masuk via NISN Siswa
            </button>
            <button type="button" wire:click="$set('activeMethod', 'otp')" class="flex-1 py-2.5 rounded-xl text-xs font-bold transition {{ $activeMethod === 'otp' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                2. OTP / WhatsApp Link
            </button>
        </div>

        @if($activeMethod === 'nisn')
            <!-- Form Masuk via NISN -->
            <form wire:submit.prevent="loginWithNisn" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nomor Induk Siswa Nasional (NISN) / Nama</label>
                    <input type="text" wire:model="nisn" placeholder="Contoh: 0071234567" class="w-full px-4 py-3 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    @error('nisn') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tanggal Lahir Anak (Opsional)</label>
                    <input type="date" wire:model="tanggalLahir" class="w-full px-4 py-3 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500 text-slate-600">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white rounded-2xl font-black text-sm shadow-xl shadow-rose-600/20 transition flex items-center justify-center gap-2">
                        <span>Masuk ke Buku Parenting</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>
        @else
            <!-- Form Masuk via OTP WhatsApp -->
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">NISN Siswa</label>
                    <input type="text" wire:model="nisn" placeholder="Masukkan NISN anak" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nomor WhatsApp Orang Tua</label>
                    <input type="text" wire:model="nomorWa" placeholder="08..." class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200">
                </div>

                @if(!$otpSent)
                    <button type="button" wire:click="requestOtp" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span>Kirim Kode OTP WhatsApp</span>
                    </button>
                @else
                    <!-- Input Verifikasi OTP -->
                    <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200">
                        <div class="text-xs text-emerald-800 font-bold mb-2">Simulasi Kode OTP yang dikirim: <span class="font-mono text-base text-emerald-950">{{ $simulatedOtpCode }}</span></div>
                        <input type="text" wire:model="otpInput" maxlength="6" placeholder="Masukkan 6 digit OTP" class="w-full px-4 py-2.5 text-center tracking-widest font-mono text-lg rounded-xl border border-emerald-300">
                        
                        <div class="mt-3 flex gap-2">
                            <button type="button" wire:click="verifyOtpSubmit" class="flex-1 py-2.5 bg-emerald-700 text-white rounded-xl font-bold text-xs">
                                Verifikasi OTP
                            </button>
                            <a href="{{ $simulatedMagicLink }}" class="px-3 py-2.5 bg-white border border-emerald-300 text-emerald-700 rounded-xl font-bold text-xs text-center">
                                Buka Langsung
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Quick Switch Demo Siswa -->
    <div class="bg-slate-50 rounded-3xl p-6 border border-slate-200/80">
        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Pilih Siswa Contoh (Simulasi Cepat Pengujian):</div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($demoSiswa as $ds)
                <button type="button" wire:click="quickLogin('{{ $ds->id }}')" class="p-3 bg-white hover:bg-rose-50 hover:border-rose-200 rounded-2xl border border-slate-200 text-left transition flex items-center justify-between group">
                    <div>
                        <div class="font-bold text-xs text-slate-800 group-hover:text-rose-700">{{ $ds->nama }}</div>
                        <div class="text-[11px] text-slate-400">NISN: {{ $ds->nisn ?? '-' }} | {{ $ds->rombel->nama_rombel ?? '-' }}</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-rose-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            @endforeach
        </div>
    </div>
</div>