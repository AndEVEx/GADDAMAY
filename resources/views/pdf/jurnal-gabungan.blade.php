<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jurnal Mengajar & Rekap KKTP - {{ $rombel->nama_kelas }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #222;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #000;
            margin-bottom: 15px;
            padding-bottom: 8px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-img {
            width: 65px;
            height: 65px;
            object-fit: contain;
        }
        .kop-text {
            text-align: center;
        }
        .kop-text h4 {
            margin: 0;
            font-size: 10pt;
            font-weight: normal;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h3 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-text h2 {
            margin: 2px 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0d47a1;
        }
        .kop-text p {
            margin: 0;
            font-size: 8pt;
            color: #444;
        }
        .doc-title {
            text-align: center;
            margin: 12px 0 10px 0;
        }
        .doc-title h4 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .doc-title p {
            margin: 2px 0 0 0;
            font-size: 9pt;
            font-weight: bold;
            color: #333;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9pt;
        }
        .info-table td {
            padding: 2px 4px;
        }
        .section-header {
            background-color: #1a56db;
            color: #fff;
            padding: 4px 8px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 6px;
            border-radius: 3px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        table.data-table th {
            background-color: #f0f4f8;
            border: 1px solid #777;
            padding: 5px 4px;
            text-align: center;
            font-weight: bold;
        }
        table.data-table td {
            border: 1px solid #999;
            padding: 4px;
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-danger { background-color: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
        .badge-warning { background-color: #fff8e1; color: #f57f17; border: 1px solid #ffe082; }
        .badge-info { background-color: #e1f5fe; color: #0277bd; border: 1px solid #81d4fa; }
        .badge-success { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 9pt;
            page-break-inside: avoid;
        }
        .signature-table td {
            vertical-align: top;
            text-align: center;
            width: 50%;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT RESMI --}}
    <table class="header-table">
        <tr>
            <td style="width: 75px; text-align: center;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                <h4>Pemerintah Daerah Provinsi Jawa Barat</h4>
                <h3>Dinas Pendidikan &bull; Cabang Dinas Wilayah IX</h3>
                <h2>SMK Negeri 2 Indramayu</h2>
                <p>Jl. Umar Rasadi, Singaraja, Kec. Indramayu, Kab. Indramayu, Jawa Barat 45218</p>
                <p>Website: smkn2indramayu.sch.id &bull; Email: info@smkn2indramayu.sch.id</p>
            </td>
        </tr>
    </table>

    {{-- JUDUL DOKUMEN --}}
    <div class="doc-title">
        <h4>JURNAL MENGAJAR GURU & REKAPITULASI KETERCAPAIAN KKTP</h4>
        <p>Periode: {{ $namaBulan }}</p>
    </div>

    {{-- IDENTITAS GURU & KELAS --}}
    <table class="info-table">
        <tr>
            <td style="width: 18%;" class="fw-bold">Nama Guru</td>
            <td style="width: 32%;">: {{ $guru->name }}</td>
            <td style="width: 18%;" class="fw-bold">Kelas / Rombel</td>
            <td style="width: 32%;">: {{ $rombel->nama_kelas }} (Tingkat {{ $rombel->tingkat }})</td>
        </tr>
        <tr>
            <td class="fw-bold">Email / Akun</td>
            <td>: {{ $guru->email }}</td>
            <td class="fw-bold">Tahun Pelajaran</td>
            <td>: {{ date('Y') }}/{{ date('Y') + 1 }}</td>
        </tr>
        <tr>
            <td class="fw-bold">Mata Pelajaran</td>
            <td>: {{ $agendas->first()?->jadwalPelajaran?->mataPelajaran?->nama_mapel ?? '-' }}</td>
            <td class="fw-bold">Total Pertemuan</td>
            <td>: {{ $agendas->count() }} Pertemuan KBM Selesai</td>
        </tr>
    </table>

    {{-- BAGIAN 1: REKAPITULASI AGENDA KBM --}}
    <div class="section-header">I. Rekapitulasi Pelaksanaan Kegiatan Belajar Mengajar (KBM)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 75px;">Hari, Tgl</th>
                <th style="width: 50px;">Waktu</th>
                <th>Materi Pokok & TP Diajarkan</th>
                <th>Refleksi Pembelajaran Guru</th>
                <th style="width: 60px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($agendas as $agenda)
            <tr>
                <td class="text-center">{{ $agenda->pertemuan_ke }}</td>
                <td>
                    <div class="fw-bold">{{ $agenda->tanggal?->translatedFormat('d/m/Y') }}</div>
                    <small style="color: #666;">{{ $agenda->tanggal?->translatedFormat('l') }}</small>
                </td>
                <td class="text-center">
                    {{ $agenda->waktu_mulai?->setTimezone('Asia/Jakarta')->format('H:i') ?? $agenda->created_at?->setTimezone('Asia/Jakarta')->format('H:i') }}
                </td>
                <td>
                    <div class="fw-bold">{{ $agenda->materi_diajarkan }}</div>
                    @if($agenda->tujuanPembelajaran->isNotEmpty())
                        <div style="margin-top: 3px; font-size: 7.8pt; color: #1a56db;">
                            TP: {{ $agenda->tujuanPembelajaran->pluck('kode_tp')->implode(', ') }}
                        </div>
                    @endif
                </td>
                <td style="font-size: 8pt; color: #444;">
                    {{ $agenda->refleksi ?: '—' }}
                </td>
                <td class="text-center">
                    <span class="badge badge-success">Selesai</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 10px; color: #777;">Belum ada riwayat KBM pada bulan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- BAGIAN 2: REKAP SISWA TIDAK HADIR --}}
    <div class="section-header">II. Rekapitulasi Ketidakhadiran Siswa saat KBM (Sakit, Izin, Alpha)</div>
    @if($absensiRecords->isNotEmpty())
        <div style="margin-bottom: 5px; font-size: 8.5pt;">
            <strong>Ringkasan Ketidakhadiran:</strong>
            Sakit: <span class="badge badge-warning">{{ $absenStats['sakit'] }}</span> &bull;
            Izin: <span class="badge badge-info">{{ $absenStats['izin'] }}</span> &bull;
            Alpha: <span class="badge badge-danger">{{ $absenStats['alpha'] }}</span> &bull;
            Total: <strong>{{ $absenStats['total'] }} kejadian absen</strong>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 75px;">Tanggal</th>
                    <th style="width: 45px;">Ptm Ke-</th>
                    <th>Nama Siswa</th>
                    <th style="width: 60px;">NIS</th>
                    <th style="width: 65px;">Status</th>
                    <th>Materi Saat Tidak Hadir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($absensiRecords as $idx => $absen)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $absen->agendaHarian?->tanggal?->translatedFormat('d/m/Y') }}</td>
                    <td class="text-center">{{ $absen->agendaHarian?->pertemuan_ke ?? '-' }}</td>
                    <td class="fw-bold">{{ $absen->siswa?->nama }}</td>
                    <td class="text-center">{{ $absen->siswa?->nis ?? '—' }}</td>
                    <td class="text-center">
                        @if($absen->status === 'sakit')
                            <span class="badge badge-warning">Sakit</span>
                        @elseif($absen->status === 'izin')
                            <span class="badge badge-info">Izin</span>
                        @else
                            <span class="badge badge-danger">Alpha</span>
                        @endif
                    </td>
                    <td style="font-size: 8pt;">{{ $absen->agendaHarian?->materi_diajarkan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="font-size: 8.5pt; color: #2e7d32; font-style: italic; margin-bottom: 10px;">
            ✓ Nihil. Seluruh siswa hadir 100% pada semua sesi KBM di bulan ini.
        </p>
    @endif

    {{-- BAGIAN 3: REKAP SISWA BELUM MENCAPAI KKTP --}}
    <div class="section-header">III. Rekapitulasi Siswa Belum Mencapai KKTP (Kriteria Ketercapaian TP)</div>
    @if($siswaBelumTuntasOnly->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 65px;">NIS</th>
                    <th style="width: 140px;">Nama Siswa</th>
                    <th>Tujuan Pembelajaran (TP) yang Belum Tuntas</th>
                    <th style="width: 75px;">Ketercapaian</th>
                    <th style="width: 90px;">Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswaBelumTuntasOnly as $idx => $s)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $s->siswa->nis ?? '—' }}</td>
                    <td class="fw-bold">{{ $s->siswa->nama }}</td>
                    <td>
                        @foreach($s->uncompleted_tps as $tp)
                            <div style="font-size: 8pt; margin-bottom: 2px;">
                                <strong style="color: #c62828;">[{{ $tp->kode_tp }}]</strong> {{ $tp->deskripsi_tp }}
                            </div>
                        @endforeach
                    </td>
                    <td class="text-center">
                        <span class="badge badge-danger">{{ $s->completed_count }}/{{ $s->total_tp }} TP ({{ $s->percentage }}%)</span>
                    </td>
                    <td class="text-center" style="font-size: 8pt; color: #555;">
                        Remedial / Bimbingan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="font-size: 8.5pt; color: #2e7d32; font-style: italic; margin-bottom: 10px;">
            ✓ Luar biasa! Seluruh siswa telah mencapai 100% Kriteria Ketercapaian Tujuan Pembelajaran (KKTP).
        </p>
    @endif

    {{-- LEMBAR PENGESAHAN --}}
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Kepala SMKN 2 Indramayu / Waka Kurikulum</strong>
                <br><br><br><br><br>
                <strong>___________________________________</strong><br>
                NIP. ....................................................
            </td>
            <td>
                Indramayu, {{ $tanggalCetak }}<br>
                <strong>Guru Mata Pelajaran</strong>
                <br><br><br><br><br>
                <strong>{{ $guru->name }}</strong><br>
                NIP/ID. {{ $guru->email }}
            </td>
        </tr>
    </table>

</body>
</html>
