<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan IKI Guru - {{ $guru->name }}</title>
    <style>
        @page {
            margin: 1.2cm 1.2cm 1.2cm 1.2cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #222;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #000;
            margin-bottom: 12px;
            padding-bottom: 6px;
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
            font-size: 9.5pt;
            font-weight: normal;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h3 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-text h2 {
            margin: 2px 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0d47a1;
        }
        .kop-text p {
            margin: 0;
            font-size: 7.5pt;
            color: #444;
        }
        .doc-title {
            text-align: center;
            margin: 10px 0 12px 0;
        }
        .doc-title h4 {
            margin: 0;
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .doc-title p {
            margin: 2px 0 0 0;
            font-size: 8.5pt;
            font-weight: bold;
            color: #333;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        .info-table td {
            padding: 2px 4px;
        }
        .section-header {
            background-color: #1a56db;
            color: #fff;
            padding: 4px 8px;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 14px;
            margin-bottom: 6px;
            border-radius: 3px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #777;
            padding: 4px 5px;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .page-break { page-break-before: always; }
        
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 8.5pt;
        }
        .ttd-table td {
            vertical-align: top;
            text-align: center;
            width: 50%;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT RESMI --}}
    <table class="header-table">
        <tr>
            <td style="width: 80px; text-align: center;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                <h4>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
                <h4>DINAS PENDIDIKAN</h4>
                <h4>CABANG DINAS PENDIDIKAN WILAYAH IX</h4>
                <h2>SMK NEGERI 2 INDRAMAYU</h2>
                <p>Jl. Umar Wirahadikusumah No. 1 Telp/Fax. (0234) 272426 Indramayu 45213</p>
                <p>Website: www.smkn2indramayu.sch.id &bull; E-mail: smkn2indramayu@gmail.com</p>
            </td>
        </tr>
    </table>

    {{-- JUDUL DOKUMEN --}}
    <div class="doc-title">
        <h4>LAPORAN INDIKATOR KINERJA INDIVIDU (IKI) GURU</h4>
        <p>PERIODE: {{ strtoupper($namaBulan) }} &bull; TAHUN AJARAN {{ date('Y') }}/{{ date('Y')+1 }}</p>
    </div>

    {{-- IDENTITAS GURU --}}
    <table class="info-table">
        <tr>
            <td style="width: 130px;"><strong>Nama Guru</strong></td>
            <td style="width: 10px;">:</td>
            <td style="width: 280px;"><strong>{{ $guru->name }}</strong></td>
            <td style="width: 110px;"><strong>Tanggal Cetak</strong></td>
            <td style="width: 10px;">:</td>
            <td>{{ $tanggalCetak }}</td>
        </tr>
        <tr>
            <td><strong>Email / Akun</strong></td>
            <td>:</td>
            <td>{{ $guru->email }}</td>
            <td><strong>Total Beban</strong></td>
            <td>:</td>
            <td><strong>{{ $totalJpSeminggu }} JP / Minggu</strong></td>
        </tr>
    </table>

    {{-- 1. TABEL JAM MENGAJAR DALAM SEMINGGU --}}
    @if($includeJadwal)
        <div class="section-header">1. Jadwal Jam Mengajar Guru Dalam Seminggu</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 70px;">Hari</th>
                    <th style="width: 80px;">Jam Ke</th>
                    <th style="width: 90px;">Waktu</th>
                    <th style="width: 110px;">Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 60px;">Beban JP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwalList as $idx => $j)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center fw-bold">{{ $j->hari_label }}</td>
                        <td class="text-center">{{ $j->jam_range }}</td>
                        <td class="text-center">{{ $j->waktu_range }}</td>
                        <td class="fw-bold">{{ $j->rombel }}</td>
                        <td>{{ $j->mapel }}</td>
                        <td class="text-center fw-bold">{{ $j->total_jp }} JP</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada jadwal mengajar terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa;">
                    <td colspan="6" class="text-end fw-bold">TOTAL BEBAN MENGAJAR SEMINGGU:</td>
                    <td class="text-center fw-bold" style="color: #0d47a1;">{{ $totalJpSeminggu }} JP</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- 2. TABEL DAFTAR NAMA SISWA YANG DIAJAR --}}
    @if($includeSiswa)
        @if($includeJadwal) <div class="page-break"></div> @endif
        <div class="section-header">2. Daftar Nama Siswa Yang Diajar (Berdasarkan Kelas & Mapel)</div>
        <p style="font-size: 7.5pt; color: #555; margin-top: -2px; margin-bottom: 8px;">
            * Kelas yang mempelajari lebih dari 1 mata pelajaran dicantumkan dalam tabel terpisah.
        </p>

        @forelse($siswaTables as $st)
            <div style="margin-top: 10px; margin-bottom: 4px; font-weight: bold; font-size: 8.5pt;">
                &bull; KELAS: {{ $st->rombel_nama }} | MATA PELAJARAN: {{ $st->mapel_nama }} (Total: {{ $st->total_siswa }} Siswa)
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th style="width: 80px;">NIS</th>
                        <th>Nama Lengkap Siswa</th>
                        <th style="width: 60px;">L/P</th>
                        <th style="width: 90px;">Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($st->siswa_list as $sIdx => $s)
                        <tr>
                            <td class="text-center">{{ $sIdx + 1 }}</td>
                            <td class="text-center">{{ $s->nis ?? '-' }}</td>
                            <td>{{ $s->nama }}</td>
                            <td class="text-center">{{ $s->jenis_kelamin ?? '-' }}</td>
                            <td class="text-center">{{ $st->rombel_nama }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data siswa di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @empty
            <p class="text-center" style="font-style: italic;">Tidak ada data kelas/siswa yang diampu.</p>
        @endforelse
    @endif

    {{-- 3. TABEL REALISASI JAM MASUK KELAS BULANAN --}}
    @if($includeKehadiran)
        <div class="page-break"></div>
        <div class="section-header">3. Rekapitulasi Realisasi Jam Masuk Kelas (Bulan: {{ $namaBulan }})</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Kelas / Rombel</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 65px;">Target JP</th>
                    <th style="width: 75px;">Realisasi JP</th>
                    <th style="width: 65px;">Izin/Sakit</th>
                    <th style="width: 65px;">Selisih</th>
                    <th style="width: 75px;">% Capaian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapKelas as $idx => $rk)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="fw-bold">{{ $rk->rombel_nama }}</td>
                        <td>{{ $rk->mapel_nama }}</td>
                        <td class="text-center">{{ $rk->target_jp }} JP</td>
                        <td class="text-center fw-bold" style="color: green;">{{ $rk->realisasi_jp }} JP</td>
                        <td class="text-center">{{ $rk->izin_jp + $rk->sakit_jp }} JP</td>
                        <td class="text-center fw-bold">{{ $rk->selisih_jp >= 0 ? '+' . $rk->selisih_jp : $rk->selisih_jp }} JP</td>
                        <td class="text-center fw-bold">{{ $rk->persentase }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada rekap mengajar di bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa;">
                    <td colspan="3" class="text-end fw-bold">TOTAL REKAPITULASI:</td>
                    <td class="text-center fw-bold">{{ $targetJpTotal }} JP</td>
                    <td class="text-center fw-bold" style="color: green;">{{ $realisasiJpTotal }} JP</td>
                    <td class="text-center fw-bold">{{ $totalIzinJp + $totalSakitJp }} JP</td>
                    <td class="text-center fw-bold">{{ ($realisasiJpTotal - $targetJpTotal) >= 0 ? '+' . ($realisasiJpTotal - $targetJpTotal) : ($realisasiJpTotal - $targetJpTotal) }} JP</td>
                    <td class="text-center fw-bold" style="color: #0d47a1;">{{ $persentaseKehadiran }}%</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- LEMBAR TANDA TANGAN GURU --}}
    <table class="ttd-table">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                Indramayu, {{ $tanggalCetak }}<br>
                Guru Mata Pelajaran,<br><br><br><br><br>
                <strong><u>{{ $guru->name }}</u></strong><br>
                @if(isset($guru->nip))
                    NIP. {{ $guru->nip }}
                @else
                    ID / Email: {{ $guru->email }}
                @endif
            </td>
        </tr>
    </table>

</body>
</html>