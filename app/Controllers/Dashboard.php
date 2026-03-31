<?php

namespace App\Controllers;
use App\Models\Absensisiswa_model;
use App\Models\Absensiguru_model;
use App\Models\Point_model;
use App\Models\Siswa_model;
use App\Models\Totalpoint_model;
use App\Models\Pesan_model;

use CodeIgniter\Controller;
date_default_timezone_set('Asia/Jakarta');
class Dashboard extends Controller
{
    public function index($id = null)
{
    $model = new Absensisiswa_model;
    $m_absenguru = new Absensiguru_model;
    $m_point = new Point_model;
    $m_siswa = new Siswa_model;
    $m_totalpoint = new Totalpoint_model;
    $db = \Config\Database::connect();

    $tgl = date('Y-m-d');

    // Ambil tapel aktif
    $row_tapel = $db->table('r_tapel')->where('sts_aktif', 1)->get()->getRow();
    if (!$row_tapel) {
        session()->setFlashdata('error', 'Tahun pelajaran aktif tidak ditemukan');
        return redirect()->to('/Dashboard');
    }
    $id_tapel = $row_tapel->id_tapel;

    // Ambil logo aplikasi
    $row_logo = $db->query("SELECT file FROM t_setting_aplikasi LIMIT 1")->getRow();
    $logo = (!empty($row_logo) && !empty($row_logo->file)) ? $row_logo->file : 'default.png';

    // Tentukan hari
    $hariMap = [
        "Sunday"    => "Minggu",
        "Monday"    => "Senin",
        "Tuesday"   => "Selasa",
        "Wednesday" => "Rabu",
        "Thursday"  => "Kamis",
        "Friday"    => "Jumat",
        "Saturday"  => "Sabtu"
    ];
    $hari = $hariMap[date('l', strtotime($tgl))] ?? '';

    // Ambil data siswa berdasarkan RFID (jika dikirim)
    $rfid = $this->request->getVar('rfid');
    $foto = null;
    $nama = null;
    $kelas = null;

    if (!empty($rfid)) {
        $row_siswa = $db->table('t_siswa')
            ->select('nm_siswa, file, id_siswa')
            ->where('rfid', $rfid)
            ->get()
            ->getRow();

        if ($row_siswa) {
            $foto = !empty($row_siswa->file)
                ? base_url('image/siswa/' . $row_siswa->file)
                : base_url('image/siswa/noimage.png');
            $nama = $row_siswa->nm_siswa;

            // ambil nama kelas (optional)
            $kelasRow = $db->table('t_siswa_rombel')
                ->select('t_rombel.nm_rombel')
                ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel', 'left')
                ->where('t_siswa_rombel.id_siswa', $row_siswa->id_siswa)
                ->where('t_siswa_rombel.id_tapel', $id_tapel)
                ->get()
                ->getRow();

            $kelas = $kelasRow->nm_rombel ?? '-';
        } else {
            session()->setFlashdata('error', 'RFID tidak ditemukan');
            return redirect()->to('/Dashboard');
        }
    }

    $data = [
        'getLogo'   => $logo,
        'getHari'   => $hari,
        'getIdtapel'=> $id_tapel,
        'stsAbsen'  => '-',
        'fotoSiswa' => $foto,
        'namaSiswa' => $nama,
        'kelasSiswa'=> $kelas
    ];

    return view('func')
        . view('absensi/kartu', $data);
}

    
    
public function addabsensi()
{
    $model = new Absensisiswa_model;
    $m_absenguru = new Absensiguru_model;
   
    $rfid = $this->request->getPost('rfid');
    $tgl = date('Y-m-d');
    $jamnow = date('H:i:s');

    echo view('func');

    // Konversi hari
    $hariInggris = date('l', strtotime($tgl));
    $hariMap = [
        "Sunday"    => "Minggu",
        "Monday"    => "Senin",
        "Tuesday"   => "Selasa",
        "Wednesday" => "Rabu",
        "Thursday"  => "Kamis",
        "Friday"    => "Jumat",
        "Saturday"  => "Sabtu"
    ];
    $hari = $hariMap[$hariInggris] ?? $hariInggris;

    $db = \Config\Database::connect();
    $id_tapel = $db->table('r_tapel')->where('sts_aktif', 1)->get()->getRow()->id_tapel ?? null;

    if (!$id_tapel) {
        session()->setFlashdata('error','Tahun pelajaran aktif tidak ditemukan');
        return redirect()->to('/Dashboard');
    }

    // cek apakah RFID terdaftar
    $siswaRow = $db->table('t_siswa')->select('id_siswa')->where('rfid', $rfid)->get()->getRow();

    if (!$siswaRow) {
        session()->setFlashdata('error','Nomor RFID tidak terdaftar');
        return redirect()->to('/Dashboard');
    }

    $id_siswa = $siswaRow->id_siswa;
    $idrombel = idsiswatoidrombel($id_siswa, $id_tapel);

    if ($idrombel <= 0) {
        session()->setFlashdata('error', 'Siswa belum mempunyai kelas');
        return redirect()->to('/Dashboard');
    }

    // Ambil jam absen
    $row_jam = $db->table('r_hari')
        ->where('nm_hari', $hari)
       
        ->get()
        ->getRow();
    $jammasuk  = $row_jam->jammasuk ?? null;
    $jampulang = $row_jam->jampulang ?? null;

    // Ambil data siswa
    $rowsiswa = $db->table('t_siswa')
        ->select('t_siswa.nm_siswa, t_siswa.no_induk, t_siswa.hp, t_rombel.nm_rombel, t_siswa.file')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('t_siswa.id_siswa', $id_siswa)
        ->where('t_siswa_rombel.id_tapel', $id_tapel)
        ->get()
        ->getRow();

    if (!$rowsiswa) {
        session()->setFlashdata('error', 'Data siswa tidak ditemukan');
        return redirect()->to('/Dashboard');
    }

    // Cek apakah sudah ada absen masuk
    $sudahMasuk = $db->table('t_siswa_hadir')
        ->where(['tgl_hadir' => $tgl, 'id_siswa' => $id_siswa, 'sts_hadir' => 0])
        ->countAllResults();

    // Cek apakah sudah pernah absen pulang
    $sudahPulang = $db->table('t_siswa_hadir')
        ->where(['tgl_hadir' => $tgl, 'id_siswa' => $id_siswa, 'sts_hadir' => 1])
        ->countAllResults();

    if ($sudahPulang > 0) {
        session()->setFlashdata('error', 'Siswa sudah absen pulang hari ini');
        return redirect()->to('/Dashboard');
    }

    if ($sudahMasuk > 0) {
        // Absen pulang
        if ($jamnow < $jampulang) {
            session()->setFlashdata('error', 'Belum waktunya pulang');
            return redirect()->to('/Dashboard');
        }

        $data = [
            'id_siswa'  => $id_siswa,
            'id_tapel'  => $id_tapel,
            'tgl_hadir' => $tgl,
            'sts_hadir' => 1,
            'jam'       => $jamnow
        ];
        $model->saveAbsensi($data);

        $stshadir = 'Pulang';
        $pesanDoa = '*MOHON DO`A SELAMAT SAMPAI DI RUMAH*';
    } else {
        // Absen masuk
        $data = [
            'id_siswa'  => $id_siswa,
            'id_tapel'  => $id_tapel,
            'tgl_hadir' => $tgl,
            'sts_hadir' => 0,
            'jam'       => $jamnow
        ];
        $model->saveAbsensi($data);

        $stshadir = ($jamnow > $jammasuk) ? 'Terlambat' : 'Hadir';
        $pesanDoa = '*MOHON DO`A DIBERIKAN KEMUDAHAN DALAM BELAJAR*';
    }

    // 🧩 siapkan foto
    $foto = !empty($rowsiswa->file) ? $rowsiswa->file : 'noimage.png';

    // Set data untuk tampilan SweetAlert
    session()->setFlashdata('Pesanabsen', $stshadir);
    session()->setFlashdata('Nama', $rowsiswa->nm_siswa);
    session()->setFlashdata('NIS', $rowsiswa->no_induk);
    session()->setFlashdata('Kelas', $rowsiswa->nm_rombel);
    session()->setFlashdata('Fotoabsen', $foto);
    session()->setFlashdata('Jamabsen', $jamnow);

    // ============================
    // KIRIM NOTIFIKASI WHATSAPP
    // ============================
    if (!empty($rowsiswa->hp)) {

        $pesanWA =
    "🏫 *SMKN 2 INDRAMAYU*\n\n" .
    "📋 *Notifikasi Absensi Siswa*\n\n" .
    "Halo Bapak/Ibu 👋\n" .
    "Kami informasikan bahwa kehadiran siswa berikut telah tercatat dalam sistem:\n\n" .
    "👤 *Nama*   : {$rowsiswa->nm_siswa}\n" .
    "🆔 *NIS*    : {$rowsiswa->no_induk}\n" .
    "🏫 *Kelas*  : {$rowsiswa->nm_rombel}\n" .
    "📅 *Hari*   : {$hari}\n" .
    "📆 *Tanggal*: " . date('d-m-Y') . "\n" .
    "⏰ *Jam*    : {$jamnow}\n" .
    "📌 *Status* : *{$stshadir}*\n\n" .
    $pesanDoa . "\n\n" .
    "Terima kasih atas perhatian dan kerja samanya 🙏\n" .
    "Semoga ananda selalu sehat dan semangat belajar.\n\n" .
    "— *Sistem Absensi Digital*\n" .
    "*SMKN 2 INDRAMAYU*";

        $this->sendWA($rowsiswa->hp, $pesanWA);
    }

    return redirect()->to('/Dashboard');
}

private function sendWA($nomor, $pesan)
{
    // Normalisasi nomor (08 -> 628)
    $nomor = preg_replace('/[^0-9]/', '', $nomor);
    if (substr($nomor, 0, 1) == '0') {
        $nomor = '62' . substr($nomor, 1);
    }
    if (substr($nomor, 0, 2) !== '62') {
        $nomor = '62' . $nomor;
    }

    try {
        $waGateway = new \App\Libraries\WaGatewayService();
        $result = $waGateway->sendMessage($nomor, $pesan, 0);

        if (!$result['success']) {
            log_message('error', 'WA GOWA Error: ' . ($result['error'] ?? 'Unknown'));
        } else {
            log_message('info', 'WA GOWA Sent to: ' . $nomor);
        }

        return $result;
    } catch (\Exception $e) {
        log_message('error', 'WA GOWA Exception: ' . $e->getMessage());
        return null;
    }
}


 
}
