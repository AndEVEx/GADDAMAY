<div class=space-y-6>
    <!-- Header Page -->
    <div class=flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-zinc-800 p-6 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-700>
        <div>
            <div class=inline-flex items-center gap-2 px-3 py-1 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-full text-xs font-semibold mb-2>
                <span class=w-2 h-2 rounded-full bg-amber-500 animate-pulse></span>
                Meja Asesor & Administrator LSP-P1
            </div>
            <h1 class=text-2xl font-black text-zinc-900 dark:text-white tracking-tight>Verifikasi & Asesmen Ujikom</h1>
            <p class=text-sm text-zinc-500 dark:text-zinc-400 mt-1>Kelola pendaftaran asesi, verifikasi portofolio APL-01/02, tentukan jadwal TUK, dan rekam hasil kompetensi.</p>
        </div>
        <div class=flex items-center gap-3>
            <a href={{ route('portal.utama') }} class=px-4 py-2 text-sm font-medium text-zinc-600 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 rounded-xl transition>
                Kembali ke Portal
            </a>
            <a href={{ route('ujikom.daftar') }} target=_blank class=px-4 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition>
                + Buka Portal Asesi
            </a>
        </div>
    </div>

    <!-- Alert Flash -->
    @if (session()->has('success'))
        <div class=p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 dark:bg-emerald-950/30 dark:border-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3>
            <svg class=w-5 h-5 flex-shrink-0 fill=currentColor viewBox=0 0 20 20><path fill-rule=evenodd d=M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z clip-rule=evenodd/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Statistic Metric Badges -->
    <div class=grid grid-cols-2 sm:grid-cols-4 gap-4>
        <div class=bg-white dark:bg-zinc-800 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm>
            <p class=text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider>Total Pendaftar</p>
            <p class=text-2xl font-black text-zinc-900 dark:text-white mt-1>{{ ['total_peserta'] }} <span class=text-xs font-normal text-zinc-400>Siswa</span></p>
        </div>
        <div class=bg-white dark:bg-zinc-800 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm>
            <p class=text-xs font-semibold text-amber-500 uppercase tracking-wider>Menunggu Verifikasi</p>
            <p class=text-2xl font-black text-amber-600 dark:text-amber-400 mt-1>{{ ['menunggu'] }} <span class=text-xs font-normal text-zinc-400>Berkas</span></p>
        </div>
        <div class=bg-white dark:bg-zinc-800 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm>
            <p class=text-xs font-semibold text-blue-500 uppercase tracking-wider>Lolos Administrasi</p>
            <p class=text-2xl font-black text-blue-600 dark:text-blue-400 mt-1>{{ ['lolos_berkas'] }} <span class=text-xs font-normal text-zinc-400>Asesi</span></p>
        </div>
        <div class=bg-white dark:bg-zinc-800 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm>
            <p class=text-xs font-semibold text-emerald-500 uppercase tracking-wider>Dinyatakan Kompeten</p>
            <p class=text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1>{{ ['kompeten'] }} <span class=text-xs font-normal text-zinc-400>Tersertifikasi</span></p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class=bg-white dark:bg-zinc-800 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between>
        <div class=flex flex-wrap gap-2 w-full md:w-auto>
            <select wire:model.live=filterSkema class=bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none>
                <option value=">Semua Skema Ujikom</option>
 @foreach ( as )
 <option value={{ ->id }}>{{ ->kode_skema }} - {{ ->nama_skema }}</option>
 @endforeach
 </select>

 <select wire:model.live=filterStatus class=bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none>
 <option value=>Semua Status Verifikasi</option>
 <option value=menunggu_verifikasi>Menunggu Verifikasi</option>
 <option value=lolos_administrasi>Lolos Administrasi</option>
 <option value=berkas_kurang>Berkas Kurang</option>
 <option value=ditolak>Ditolak</option>
 </select>
 </div>

 <div class=w-full md:w-72>
 <input type=text wire:model.live.debounce.300ms=search placeholder=Cari NIS, Nama, No. Registrasi... class=w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none>
 </div>
 </div>

 <!-- Table Peserta Ujikom -->
 <div class=bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden>
 <div class=overflow-x-auto>
 <table class=w-full text-left border-collapse text-sm>
 <thead>
 <tr class=border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50 text-xs text-zinc-500 uppercase font-semibold>
 <th class=p-4>No. Registrasi & Asesi</th>
 <th class=p-4>Skema Sertifikasi</th>
 <th class=p-4>Berkas Pra-Asesmen</th>
 <th class=p-4>Status Verifikasi</th>
 <th class=p-4>Jadwal & TUK</th>
 <th class=p-4>Hasil Ujikom</th>
 <th class=p-4 text-right>Aksi</th>
 </tr>
 </thead>
 <tbody class=divide-y divide-zinc-200 dark:divide-zinc-700>
 @forelse ( as )
 <tr class=hover:bg-zinc-50 dark:hover:bg-zinc-750 transition>
 <td class=p-4>
 <span class=font-mono font-bold text-indigo-600 dark:text-indigo-400 text-xs>{{ ->nomor_pendaftaran }}</span>
 <p class=font-bold text-zinc-900 dark:text-white>{{ ->siswa?->nama ?? 'Siswa Tidak Ditemukan' }}</p>
 <p class=text-xs text-zinc-500>NIS: {{ ->siswa?->nis ?? '-' }} • {{ ->siswa?->rombel?->nama_rombel ?? 'Rombel -' }}</p>
 </td>
 <td class=p-4>
 <p class=font-semibold text-zinc-800 dark:text-zinc-200 text-xs>{{ ->skema?->nama_skema }}</p>
 <span class=text-[10px] font-mono text-zinc-500>{{ ->skema?->kode_skema }} ({{ ->skema?->jurusan }})</span>
 </td>
 <td class=p-4>
 <div class=flex items-center gap-1>
 <span class=px-2 py-0.5 rounded-full text-xs font-semibold {{ ->berkas->count() > 0 ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300' : 'bg-zinc-100 text-zinc-500' }}>
 {{ ->berkas->count() }} Berkas
 </span>
 </div>
 </td>
 <td class=p-4>
 @if (->status_verifikasi === 'lolos_administrasi')
 <span class=px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300>Lolos Administrasi</span>
 @elseif (->status_verifikasi === 'menunggu_verifikasi')
 <span class=px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300>Menunggu</span>
 @elseif (->status_verifikasi === 'berkas_kurang')
 <span class=px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300>Berkas Kurang</span>
 @else
 <span class=px-2.5 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300>Ditolak</span>
 @endif
 </td>
 <td class=p-4 text-xs>
 @if (->jadwal_asesmen)
 <p class=font-medium text-zinc-800 dark:text-zinc-200>{{ ->jadwal_asesmen->format('d M Y') }}</p>
 <p class=text-zinc-500>{{ ->tempat_uji_kompetensi_tuk ?? 'TUK Belum Diset' }}</p>
 @else
 <span class=text-zinc-400 italic>Belum Dijadwalkan</span>
 @endif
 </td>
 <td class=p-4>
 @if (->hasil_asesmen === 'kompeten')
 <span class=inline-flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400>
 <svg class=w-4 h-4 fill=currentColor viewBox=0 0 20 20><path fill-rule=evenodd d=M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z clip-rule=evenodd/></svg>
 Kompeten (K)
 </span>
 @elseif (->hasil_asesmen === 'belum_kompeten')
 <span class=inline-flex items-center gap-1 text-xs font-bold text-rose-600 dark:text-rose-400>
 <svg class=w-4 h-4 fill=currentColor viewBox=0 0 20 20><path fill-rule=evenodd d=M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 101.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z clip-rule=evenodd/></svg>
 Belum Kompeten (BK)
 </span>
 @else
 <span class=text-xs text-zinc-400 italic>Belum Dinilai</span>
 @endif
 </td>
 <td class=p-4 text-right space-x-1>
 @if (->status_verifikasi === 'menunggu_verifikasi')
 <button wire:click=quickApprove('{{ ->id }}') class=p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition title=Approve Administrasi Cepat>
 <svg class=w-5 h-5 fill=none stroke=currentColor viewBox=0 0 24 24><path stroke-linecap=round stroke-linejoin=round stroke-width=2 d=M5 13l4 4L19 7/></svg>
 </button>
 @endif
 <button wire:click=selectPendaftaran('{{ ->id }}') class=px-3 py-1.5 bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 rounded-xl text-xs font-bold hover:opacity-90 transition>
 Detail / Asesmen
 </button>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan=7 class=p-8 text-center text-zinc-400>
 Belum ada pendaftaran asesi Ujikom yang cocok dengan filter pencarian.
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 <div class=p-4 border-t border-zinc-200 dark:border-zinc-700>
 {{ ->links() }}
 </div>
 </div>

 <!-- Modal Detail & Penetapan Hasil Asesmen -->
 @if ()
 <div class=fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4>
 <div class=bg-white dark:bg-zinc-800 w-full max-w-3xl rounded-3xl p-6 shadow-2xl border border-zinc-200 dark:border-zinc-700 space-y-6>
 <div class=flex items-start justify-between border-b border-zinc-200 dark:border-zinc-700 pb-4>
 <div>
 <span class=text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400>{{ ->nomor_pendaftaran }}</span>
 <h2 class=text-xl font-black text-zinc-900 dark:text-white>{{ ->siswa?->nama }}</h2>
 <p class=text-xs text-zinc-500>Skema: {{ ->skema?->nama_skema }} ({{ ->skema?->jurusan }})</p>
 </div>
 <button wire:click=closeDetail class=p-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-white rounded-xl>
 <svg class=w-6 h-6 fill=none stroke=currentColor viewBox=0 0 24 24><path stroke-linecap=round stroke-linejoin=round stroke-width=2 d=M6 18L18 6M6 6l12 12/></svg>
 </button>
 </div>

 <!-- Berkas Pra-Asesmen yang Diunggah -->
 <div>
 <h3 class=text-sm font-bold text-zinc-900 dark:text-white mb-2>Berkas Pra-Asesmen (Portofolio Bukti Kompetensi)</h3>
 <div class=space-y-2>
 @forelse (->berkas as )
 <div class=flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700>
 <div class=flex items-center gap-3>
 <div class=w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400>
 <svg class=w-4 h-4 fill=currentColor viewBox=0 0 20 20><path fill-rule=evenodd d=M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z clip-rule=evenodd/></svg>
 </div>
 <div>
 <p class=text-xs font-bold text-zinc-900 dark:text-white>{{ ->nama_file }}</p>
 <p class=text-[10px] text-zinc-500 uppercase>{{ str_replace('_', ' ', ->jenis_berkas) }}</p>
 </div>
 </div>
 <a href={{ asset('storage/' . ->file_path) }} target=_blank class=px-3 py-1 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 text-zinc-800 dark:text-white rounded-lg text-xs font-semibold>
 Buka File
 </a>
 </div>
 @empty
 <p class=text-xs text-zinc-400 italic p-3 bg-zinc-50 dark:bg-zinc-900 rounded-xl>Asesi belum mengunggah dokumen formulir APL-01/02 atau portofolio.</p>
 @endforelse
 </div>
 </div>

 <!-- Form Verifikasi & Asesmen -->
 <form wire:submit.prevent=simpanVerifikasi class=space-y-4 pt-2>
 <div class=grid grid-cols-1 md:grid-cols-2 gap-4>
 <div>
 <label class=block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1>Status Verifikasi Administrasi</label>
 <select wire:model=status_verifikasi class=w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none>
 <option value=menunggu_verifikasi>Menunggu Verifikasi</option>
 <option value=lolos_administrasi>Lolos Administrasi (Siap Asesmen)</option>
 <option value=berkas_kurang>Berkas Kurang Lengkap</option>
 <option value=ditolak>Ditolak</option>
 </select>
 </div>

 <div>
 <label class=block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1>Asesor Penguji</label>
 <select wire:model=asesor_id class=w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none>
 <option value=>-- Pilih Asesor LSP --</option>
 @foreach ( as )
 <option value={{ ->id }}>{{ ->name }} ({{ ->role }})</option>
 @endforeach
 </select>
 </div>

 <div>
 <label class=block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1>Jadwal Asesmen Lapangan / Ujian Praktik</label>
 <input type=date wire:model=jadwal_asesmen class=w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none>
 </div>

 <div>
 <label class=block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1>Tempat Uji Kompetensi (TUK)</label>
 <input type=text wire:model=tempat_uji_kompetensi_tuk placeholder=Contoh: Lab Komputer RPL 1 class=w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none>
 </div>
 </div>

 <div class=border-t border-zinc-200 dark:border-zinc-700 pt-4>
 <label class=block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1>Keputusan Akhir Hasil Asesmen Ujikom</label>
 <div class=grid grid-cols-3 gap-3>
 <label class=flex items-center gap-2 p-3 border rounded-xl cursor-pointer {{  === 'belum_dinilai' ? 'border-zinc-500 bg-zinc-50 dark:bg-zinc-700/50' : 'border-zinc-200 dark:border-zinc-700' }}>
 <input type=radio wire:model=hasil_asesmen value=belum_dinilai class=text-zinc-600>
 <span class=text-xs font-bold text-zinc-700 dark:text-zinc-300>Belum Dinilai</span>
 </label>
 <label class=flex items-center gap-2 p-3 border rounded-xl cursor-pointer {{  === 'kompeten' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800' : 'border-zinc-200 dark:border-zinc-700' }}>
 <input type=radio wire:model=hasil_asesmen value=kompeten class=text-emerald-600>
 <span class=text-xs font-bold text-emerald-700 dark:text-emerald-400>Kompeten (K)</span>
 </label>
 <label class=flex items-center gap-2 p-3 border rounded-xl cursor-pointer {{  === 'belum_kompeten' ? 'border-rose-500 bg-rose-50 dark:bg-rose-950/40 text-rose-800' : 'border-zinc-200 dark:border-zinc-700' }}>
 <input type=radio wire:model=hasil_asesmen value=belum_kompeten class=text-rose-600>
 <span class=text-xs font-bold text-rose-700 dark:text-rose-400>Belum Kompeten (BK)</span>
 </label>
 </div>
 </div>

 <div>
 <label class=block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1>Catatan & Umpan Balik Asesor</label>
 <textarea wire:model=catatan_asesor rows=3 placeholder=Tuliskan rekomendasi atau catatan perbaikan... class=w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none></textarea>
 </div>

 <div class=flex justify-end gap-3 pt-2>
 <button type=button wire:click=closeDetail class=px-5 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 font-semibold text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700 transition>
 Batal
 </button>
 <button type=submit class=px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md transition>
 Simpan Hasil Verifikasi & Asesmen
 </button>
 </div>
 </form>
 </div>
 </div>
 @endif
</div>