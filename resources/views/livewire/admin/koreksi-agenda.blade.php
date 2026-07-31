<div>
    <div class="page-header">
        <h1><i class="bi bi-pencil-square me-2"></i>Koreksi Agenda</h1>
        <p class="subtitle mb-0">Tandai guru izin/cuti dan assign pengganti</p>
    </div>

    {{-- Date Picker --}}
    <div class="card mb-3">
        <div class="card-body p-3">
            <label class="form-label small">Tanggal</label>
            <input type="date" wire:model.live="tanggal" class="form-control">
        </div>
    </div>

    {{-- Existing Agendas --}}
    @if($agendas->count())
    <h6 class="fw-bold mb-2"><i class="bi bi-journal-check me-2"></i>Agenda Tercatat</h6>
    @foreach($agendas as $agenda)
    <div class="card mb-2 {{ $selectedAgendaId === $agenda->id ? 'border-primary' : '' }}">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-bold small">{{ $agenda->guru?->name }}</div>
                    <div class="text-muted small">{{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }} — {{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}</div>
                    <span class="status-badge status-{{ match($agenda->status_kehadiran_guru) { 'hadir' => 'hijau', 'izin' => 'oranye', 'cuti' => 'oranye', 'sakit' => 'oranye', default => 'abu' } }} mt-1">
                        {{ ucfirst($agenda->status_kehadiran_guru) }}
                    </span>
                    @if($agenda->koreksiOleh)
                    <div class="text-muted mt-1" style="font-size: 0.7rem;">Dikoreksi: {{ $agenda->koreksiOleh->name }}</div>
                    @endif
                </div>
                <button wire:click="selectAgenda('{{ $agenda->id }}')" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil"></i>
                </button>
            </div>

            @if($selectedAgendaId === $agenda->id)
            <div class="mt-3 p-3 bg-light rounded-3 animate-fade-in-up">
                <div class="fw-bold small mb-2">Pilih Aksi:</div>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button wire:click="tandaiIzin('izin')" class="btn btn-warning btn-sm"><i class="bi bi-envelope"></i> Izin</button>
                    <button wire:click="tandaiIzin('cuti')" class="btn btn-warning btn-sm"><i class="bi bi-calendar-x"></i> Cuti</button>
                    <button wire:click="tandaiIzin('sakit')" class="btn btn-warning btn-sm"><i class="bi bi-thermometer"></i> Sakit</button>
                    <button wire:click="batalkanJadwal" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle"></i> Batalkan</button>
                </div>

                <div class="mb-2">
                    <label class="form-label small">Guru Pengganti (opsional)</label>
                    <select wire:model="guruPenggantiId" class="form-select form-select-sm">
                        <option value="">— Pilih Guru Pengganti —</option>
                        @foreach($guruList as $guru)
                        <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if($guruPenggantiId)
                <button wire:click="assignPengganti" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-person-check"></i> Tetapkan Pengganti
                </button>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endforeach
    @endif

    {{-- Jadwal Without Agenda (guru belum mulai) --}}
    @if($jadwalTanpaAgenda->count())
    <h6 class="fw-bold mb-2 mt-3"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Jadwal Tanpa Agenda</h6>
    @foreach($jadwalTanpaAgenda as $jadwal)
    <div class="card mb-2 border-danger border-opacity-25">
        <div class="card-body p-3">
            <div class="fw-bold small">{{ $jadwal->jadwalGuru->first()?->guru?->name ?? 'N/A' }}</div>
            <div class="text-muted small">{{ $jadwal->mataPelajaran?->nama_mapel }} — {{ $jadwal->rombel?->nama_kelas }}</div>
            <div class="text-muted small">Jam {{ $jadwal->jam_ke_mulai }}-{{ $jadwal->jam_ke_selesai }}</div>
            <span class="status-badge status-merah mt-1">Belum Handshake</span>
        </div>
    </div>
    @endforeach
    @endif

    @if(!$agendas->count() && !$jadwalTanpaAgenda->count())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-calendar-check" style="font-size: 2rem;"></i>
            <p class="mt-2 mb-0">Tidak ada data untuk tanggal ini.</p>
        </div>
    </div>
    @endif
</div>
