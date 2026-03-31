<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Siswa_model;
use App\Models\Absensisiswa_model;
use App\Models\Absensi_model;

date_default_timezone_set('Asia/Jakarta');
class ScanQr extends Controller
{
    protected $siswaModel;
    protected $absenModel;

    public function __construct()
    {
        $this->siswaModel = new Siswa_model();
        $this->absenModel = new Absensisiswa_model();
    }

    public function index()
    {
        // Ambil logo
        $db = \Config\Database::connect();
        $query = $db->query("SELECT file FROM t_setting_aplikasi");
        $row = $query->getRow();

        $data = [
            'getLogo' => $row->file
        ];

        echo view('scan_qr', $data);
    }
 
    public function process()
{
    function tapelaktif()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT id_tapel FROM r_tapel WHERE sts_aktif = '1'");
        $row = $query->getRow();
        return $row ? $row->id_tapel : null;
    }

    // 🔹 Fungsi format tanggal lokal (fallback jika helper tidak tersedia)
    function formatTanggal($tanggal)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $pecah = explode('-', $tanggal);
        return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
    }

    $model = new Absensisiswa_model;
    $m_siswa = new Siswa_model;
    $db = \Config\Database::connect();

    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
    }

    $json = $this->request->getJSON();
    $kode = $json->kode ?? null;

    if (!$kode) {
        return $this->response->setJSON(['status' => false, 'message' => 'QR tidak terbaca']);
    }

    // 🔹 Trim whitespace & leading zeros for matching
    $kode = trim($kode);
    $kodeTrimmed = ltrim($kode, '0');

    // 🔹 Cari siswa: rfid -> no_induk -> nisn
    $siswa = $m_siswa->where('rfid', $kode)->first();
    if (!$siswa && $kodeTrimmed !== $kode) {
        $siswa = $m_siswa->where('rfid', $kodeTrimmed)->first();
    }
    if (!$siswa) {
        $siswa = $m_siswa->where('no_induk', $kode)->first();
    }
    if (!$siswa && $kodeTrimmed !== $kode) {
        $siswa = $m_siswa->where('no_induk', $kodeTrimmed)->first();
    }
    if (!$siswa) {
        $siswa = $m_siswa->where('nisn', $kode)->first();
    }
    if (!$siswa) {
        return $this->response->setJSON(['status' => false, 'message' => 'Siswa tidak ditemukan (kode: ' . $kode . ')']);
    }

    $id_siswa = $siswa['id_siswa'];
    $id_tapel = tapelaktif();
    $tanggal = date('Y-m-d');
    $jamnow = date('H:i:s');

    // 🔹 Ambil data kelas siswa
    $queryKelas = $db->table('t_siswa_rombel tsr')
        ->select('tr.nm_rombel, tr.id_rombel')
        ->join('t_rombel tr', 'tsr.id_rombel = tr.id_rombel')
        ->where('tsr.id_siswa', $id_siswa)
        ->where('tsr.id_tapel', $id_tapel)
        ->get();
    $kelas = $queryKelas->getRow();

    $nama_kelas = $kelas ? $kelas->nm_rombel : '-';
    $id_rombel = $kelas ? $kelas->id_rombel : null;

    if (!$id_rombel) {
        return $this->response->setJSON(['status' => false, 'message' => 'Rombel tidak ditemukan']);
    }

    // 🔹 Ambil jadwal dari r_hari
    $mapHari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    $hariSekarang = $mapHari[date('l')] ?? null;
    $jadwal = $db->table('r_hari')->where('nm_hari', $hariSekarang)->get()->getRow();

    if (!$jadwal) {
        return $this->response->setJSON(['status' => false, 'message' => 'Jadwal hari ini belum diatur']);
    }

    $jamMasuk = $jadwal->jammasuk;
    $jamPulang = $jadwal->jampulang;

    // 🔹 Cek apakah sudah absen masuk
    $absenMasuk = $model->where('id_siswa', $id_siswa)
        ->where('tgl_hadir', $tanggal)
        ->where('sts_hadir', 0)
        ->first();

    // 🔹 Ambil data lengkap siswa (untuk kirim WA)
    $querysiswa = $db->query("
        SELECT nm_siswa, no_induk, hp, nm_rombel
        FROM t_siswa 
        JOIN t_siswa_rombel ON t_siswa_rombel.id_siswa = t_siswa.id_siswa
        JOIN t_rombel ON t_rombel.id_rombel = t_siswa_rombel.id_rombel
        WHERE t_siswa.id_siswa='$id_siswa' AND t_siswa_rombel.id_tapel='$id_tapel'
    ");
    $rowsiswa = $querysiswa->getRow();

    // Pastikan nomor HP valid (format 628xxxx)
    $nomorWA = preg_replace('/[^0-9]/', '', $rowsiswa->hp ?? '');
    if (strpos($nomorWA, '62') !== 0 && strlen($nomorWA) > 0) {
        $nomorWA = '62' . ltrim($nomorWA, '0');
    }

    if (!$absenMasuk) {
        // 🔹 Catat absen masuk
        $data = [
            'id_siswa' => $id_siswa,
            'id_tapel' => $id_tapel,
            'tgl_hadir' => $tanggal,
            'sts_hadir' => 0,
            'jam' => $jamnow
        ];
        $model->saveAbsensi($data);

        $terlambat = ($jamnow > $jamMasuk) ? ' (Terlambat)' : '';

        // 🔹 Kirim notifikasi WA (Masuk)
        $pesan = "
📢 *Pemberitahuan Absensi Masuk*
Tanggal: *" . formatTanggal($tanggal) . "*
No. Induk: *{$rowsiswa->no_induk}*
Nama: *{$rowsiswa->nm_siswa}*
Kelas: *{$rowsiswa->nm_rombel}*
Status: *Masuk{$terlambat}*
Jam: *{$jamnow}*

Terima kasih 🙏
*SMKN 2 INDRAMAYU*
        ";
        if ($nomorWA) $this->kirimWA($nomorWA, $pesan);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Absensi masuk berhasil' . $terlambat,
            'status_absen' => 'Masuk',
            'siswa' => [
                'nm_siswa' => $rowsiswa->nm_siswa ?? '-',
                'no_induk' => $rowsiswa->no_induk ?? '-',
                'file' => $siswa['file'] ?? null
            ],
            'kelas' => $rowsiswa->nm_rombel ?? '-',
            'tanggal' => formatTanggal($tanggal),
            'jam' => $jamnow
        ]);
        
    } else {
        // 🔹 Sudah absen masuk, cek apakah sudah waktunya pulang
        if ($jamnow >= $jamPulang) {
            $absenPulang = $model->where('id_siswa', $id_siswa)
                ->where('tgl_hadir', $tanggal)
                ->where('sts_hadir', 1)
                ->first();

            if ($absenPulang) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Siswa sudah absen pulang hari ini',
                    'status_absen' => 'Sudah Pulang'
                ]);
            }

            // 🔹 Simpan absen pulang
            $data = [
                'id_siswa' => $id_siswa,
                'id_tapel' => $id_tapel,
                'tgl_hadir' => $tanggal,
                'sts_hadir' => 1,
                'jam' => $jamnow
            ];
            $model->saveAbsensi($data);

            // 🔹 Kirim notifikasi WA (Pulang)
            $pesan = "
📢 *Pemberitahuan Absensi Pulang*
Tanggal: *" . formatTanggal($tanggal) . "*
No. Induk: *{$rowsiswa->no_induk}*
Nama: *{$rowsiswa->nm_siswa}*
Kelas: *{$rowsiswa->nm_rombel}*
Status: *Pulang*
Jam: *{$jamnow}*

Terima kasih 🙏
*SMKN 2 INDRAMAYU*
            ";
            if ($nomorWA) $this->kirimWA($nomorWA, $pesan);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Absensi pulang berhasil',
                'status_absen' => 'Pulang',
                'siswa' => [
                    'nm_siswa' => $rowsiswa->nm_siswa ?? '-',
                    'no_induk' => $rowsiswa->no_induk ?? '-',
                    'file' => $siswa['file'] ?? null
                ],
                'kelas' => $rowsiswa->nm_rombel ?? '-',
                'tanggal' => formatTanggal($tanggal),
                'jam' => $jamnow
            ]);
            
        } else {
           
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Belum waktunya pulang',
                'status_absen' => 'Tunggu Pulang',
                'siswa' => [
                    'nm_siswa' => $rowsiswa->nm_siswa ?? '-',
                    'no_induk' => $rowsiswa->no_induk ?? '-',
                    'file' => $siswa['file'] ?? null
                ],
                'kelas' => $rowsiswa->nm_rombel ?? '-',
                'tanggal' => formatTanggal($tanggal),
                'jam' => $jamnow
            ]);
            
        }
    }
}

private function kirimWA($target, $pesan)
{
    try {
        $waGateway = new \App\Libraries\WaGatewayService();
        $result = $waGateway->sendMessage($target, $pesan, 0);
        if (!$result['success']) {
            log_message('error', 'ScanQr WA Error: ' . ($result['error'] ?? 'Unknown'));
        }
        return $result;
    } catch (\Exception $e) {
        log_message('error', 'ScanQr WA Exception: ' . $e->getMessage());
        return null;
    }
}



}
