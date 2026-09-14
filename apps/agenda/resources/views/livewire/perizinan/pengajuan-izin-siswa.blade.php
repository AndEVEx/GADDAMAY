<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <a href="{{ route('perizinan.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Perizinan
                </a>
                <span class="badge bg-primary-subtle text-primary border px-3 py-2">
                    <i class="bi bi-shield-check me-1"></i> Formulir Perizinan Siswa
                </span>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-1 text-dark">Entri Pengajuan Izin / Dispensasi Siswa</h5>
                    <p class="text-muted small">Catat izin sakit, keperluan keluarga, dispensasi lomba, atau izin keluar kampus.</p>
                </div>
                <div class="card-body p-4">
                    <form wire:submit="submitPengajuan">
                        {{-- 1. Pilih Siswa --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">1. Siswa yang Mengajukan Izin <span class="text-danger">*</span></label>
                            
                            @if($selectedSiswa)
                                <div class="alert alert-success d-flex align-items-center justify-content-between mb-0">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success text-white rounded-circle p-2 me-3">
                                            <i class="bi bi-person-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $selectedSiswa->nama_siswa }}</h6>
                                            <div class="text-muted small">NIS: {{ $selectedSiswa->nis }} | Kelas: {{ $selectedSiswa->rombel?->nama_kelas }}</div>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="clearSelectedSiswa" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-circle me-1"></i> Ganti Siswa
                                    </button>
                                </div>
                            @else
                                <div class="position-relative">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                        <input type="text" wire:model.live.debounce.300ms="searchSiswa" class="form-control" placeholder="Ketik nama atau NIS siswa untuk mencari...">
                                    </div>
                                    @if(strlen(trim($searchSiswa)) >= 2 && $siswaResults->count() > 0)
                                        <div class="list-group position-absolute w-100 shadow-lg mt-1" style="z-index: 1050; max-height: 250px; overflow-y: auto;">
                                            @foreach($siswaResults as $s)
                                                <button type="button" wire:click="selectSiswa('{{ $s->id }}')" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $s->nama_siswa }}</div>
                                                        <small class="text-muted">NIS: {{ $s->nis }}</small>
                                                    </div>
                                                    <span class="badge bg-secondary">{{ $s->rombel?->nama_kelas ?? '-' }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @elseif(strlen(trim($searchSiswa)) >= 2 && $siswaResults->count() === 0)
                                        <div class="p-2 text-muted small mt-1 bg-light rounded">
                                            Tidak ditemukan siswa dengan kata kunci "{{ $searchSiswa }}".
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @error('selectedSiswaId') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- 2. Kategori Izin --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">2. Kategori Perizinan <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="form-check p-3 border rounded-3 @if($kategori === 'sakit') border-warning bg-warning bg-opacity-10 @endif">
                                        <input class="form-check-input" type="radio" wire:model.live="kategori" value="sakit" id="kat_sakit">
                                        <label class="form-check-label fw-semibold" for="kat_sakit">
                                            <i class="bi bi-heart-pulse text-warning me-1"></i> Izin Sakit
                                            <div class="text-muted small fw-normal">Sakit di rumah / rawat inap (surat dokter).</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check p-3 border rounded-3 @if($kategori === 'izin_keperluan') border-info bg-info bg-opacity-10 @endif">
                                        <input class="form-check-input" type="radio" wire:model.live="kategori" value="izin_keperluan" id="kat_keperluan">
                                        <label class="form-check-label fw-semibold" for="kat_keperluan">
                                            <i class="bi bi-envelope-open text-info me-1"></i> Izin Keperluan Keluarga
                                            <div class="text-muted small fw-normal">Urusan darurat keluarga / izin resmi ortu.</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check p-3 border rounded-3 @if($kategori === 'dispensasi_sekolah') border-primary bg-primary bg-opacity-10 @endif">
                                        <input class="form-check-input" type="radio" wire:model.live="kategori" value="dispensasi_sekolah" id="kat_dispensasi">
                                        <label class="form-check-label fw-semibold" for="kat_dispensasi">
                                            <i class="bi bi-trophy text-primary me-1"></i> Dispensasi Lomba / Sekolah
                                            <div class="text-muted small fw-normal">Tugas OSIS, perlombaan, atau kegiatan sekolah.</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check p-3 border rounded-3 @if($kategori === 'izin_keluar_kampus') border-danger bg-danger bg-opacity-10 @endif">
                                        <input class="form-check-input" type="radio" wire:model.live="kategori" value="izin_keluar_kampus" id="kat_keluar">
                                        <label class="form-check-label fw-semibold" for="kat_keluar">
                                            <i class="bi bi-door-open text-danger me-1"></i> Izin Keluar Lingkungan Sekolah
                                            <div class="text-muted small fw-normal">Izin sementara / surat jalan meja piket.</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Tanggal & Jam --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">3. Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Tanggal Mulai:</label>
                                    <input type="date" wire:model.live="tanggalMulai" class="form-control">
                                    @error('tanggalMulai') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Tanggal Selesai:</label>
                                    <input type="date" wire:model.live="tanggalSelesai" class="form-control">
                                    @error('tanggalSelesai') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            @if($kategori === 'izin_keluar_kampus')
                                <div class="row g-2 mt-2 p-3 bg-light rounded-3">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Jam Keluar Sekolah:</label>
                                        <input type="time" wire:model="jamMulai" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Jam Kembali / Selesai:</label>
                                        <input type="time" wire:model="jamSelesai" class="form-control">
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- 4. Alasan --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">4. Alasan Lengkap <span class="text-danger">*</span></label>
                            <textarea wire:model="alasan" class="form-control" rows="3" placeholder="Contoh: Sakit demam dan flu, istirahat di rumah sesuai anjuran dokter..."></textarea>
                            @error('alasan') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- 5. Lampiran Berkas --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">5. Berkas Lampiran (Foto Surat Dokter / Ortu / Tugas)</label>
                            <input type="file" wire:model="fileLampiran" class="form-control" accept="image/*,application/pdf">
                            <div class="text-muted small mt-1">Format: JPG, PNG, PDF. Maksimal 3MB.</div>
                            @error('fileLampiran') <span class="text-danger small">{{ $message }}</span> @enderror
                            
                            <div wire:loading wire:target="fileLampiran" class="text-primary small mt-1">
                                <span class="spinner-border spinner-border-sm me-1"></span> Mengunggah berkas...
                            </div>
                        </div>

                        {{-- 6. Data Pemohon & Nomor WA --}}
                        <div class="mb-4 p-3 bg-light rounded-3">
                            <label class="form-label fw-semibold text-dark mb-2">6. Kontak Pemohon (Untuk Notifikasi WhatsApp)</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Nama Pemohon:</label>
                                    <input type="text" wire:model="namaPemohon" class="form-control form-control-sm" placeholder="Nama orang tua / guru piket">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Hubungan Pemohon:</label>
                                    <select wire:model="hubunganPemohon" class="form-select form-select-sm">
                                        <option value="orang_tua">Orang Tua / Wali</option>
                                        <option value="siswa">Siswa Mandiri</option>
                                        <option value="guru_piket">Guru Piket / Petugas</option>
                                        <option value="wali_kelas">Wali Kelas</option>
                                        <option value="pembina_ekskul">Pembina Ekskul / Lomba</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">No. WhatsApp Pemohon:</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="bi bi-whatsapp text-success"></i></span>
                                        <input type="text" wire:model="nomorWaPemohon" class="form-control" placeholder="08xxxxxxxxxx">
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Akan dikirimi konfirmasi status izin.</small>
                                </div>
                            </div>
                        </div>

                        {{-- Direct Approval Toggle if staff --}}
                        @if(auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isGuruPiket() || auth()->user()->isGuru()))
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" wire:model="langsungSetujui" id="switchSetujui">
                                <label class="form-check-label fw-semibold text-success" for="switchSetujui">
                                    <i class="bi bi-check2-circle me-1"></i> Langsung Sahkan Izin Ini & Kunci Kehadiran di Agenda KBM
                                </label>
                            </div>
                        @endif

                        {{-- Submit Button --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="bi bi-send-check me-1"></i> Simpan & Kirim Pengajuan</span>
                                <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span> Menyimpan Pengajuan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>