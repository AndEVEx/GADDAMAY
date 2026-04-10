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
        $rfid = trim($rfid);
        $row_siswa = $db->table('t_siswa')
            ->select('nm_siswa, file, id_siswa')
            ->where('rfid', $rfid)
            ->get()
            ->getRow();

        // Retry without leading zeros
        if (!$row_siswa) {
            $rfidTrimmed = ltrim($rfid, '0');
            if ($rfidTrimmed !== $rfid) {
                $row_siswa = $db->table('t_siswa')
                    ->select('nm_siswa, file, id_siswa')
                    ->where('rfid', $rfidTrimmed)
                    ->get()
                    ->getRow();
            }
        }

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
   
    $rfid = trim($this->request->getPost('rfid'));
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

    // cek apakah RFID terdaftar (try exact match, then without leading zeros)
    $siswaRow = $db->table('t_siswa')->select('id_siswa')->where('rfid', $rfid)->get()->getRow();
    if (!$siswaRow) {
        $rfidTrimmed = ltrim($rfid, '0');
        if ($rfidTrimmed !== $rfid) {
            $siswaRow = $db->table('t_siswa')->select('id_siswa')->where('rfid', $rfidTrimmed)->get()->getRow();
        }
    }

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
        // Absen masuk - cek batas waktu 08:00
        if ($jamnow > '08:00:00') {
            session()->setFlashdata('error', 'Batas waktu absen pagi telah lewat (08:00)');
            return redirect()->to('/Dashboard');
        }

        $data = [
            'id_siswa'  => $id_siswa,
            'id_tapel'  => $id_tapel,
            'tgl_hadir' => $tgl,
            'sts_hadir' => 0,
            'jam'       => $jamnow
        ];
        $model->saveAbsensi($data);

        $stshadir = ($jamnow > $jammasuk) ? 'Terlambat' : 'Hadir';
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

    // WA TIDAK dikirim saat scan.
    // Notifikasi ke orangtua HANYA via WeeklyReport (Jumat sore)

    return redirect()->to('/Dashboard');
}

// sendWA removed - WA notifications are now ONLY sent via WeeklyReport (Fridays)


 
    public function getDetailKelasAjax($id_rombel)
    {
        $db = \Config\Database::connect();
        $tgl = date('Y-m-d');
        $id_tapel = session()->get('id_tapel');
        
        $nmhari = date ("D");
        switch($nmhari){
            case 'Sun': $hari_ini = "Minggu"; break;
            case 'Mon': $hari_ini = "Senin"; break;
            case 'Tue': $hari_ini = "Selasa"; break;
            case 'Wed': $hari_ini = "Rabu"; break;
            case 'Thu': $hari_ini = "Kamis"; break;
            case 'Fri': $hari_ini = "Jumat"; break;
            case 'Sat': $hari_ini = "Sabtu"; break;
            default: $hari_ini = "Tidak di ketahui"; break;
        }

        $query_jam = $db->query("SELECT jammasuk FROM r_hari where nm_hari='$hari_ini'");
        $row_jam = $query_jam->getRow();
        $jammasuk = $row_jam->jammasuk ?? '07:10:00';

        // Get all students for this class
        $querySiswa = $db->query("
            SELECT s.id_siswa, s.nm_siswa 
            FROM t_siswa_rombel sr
            JOIN t_siswa s ON s.id_siswa = sr.id_siswa
            WHERE sr.id_rombel = '$id_rombel' 
            AND sr.id_tapel = '$id_tapel'
            AND s.sts_siswa = 1
            ORDER BY s.nm_siswa ASC
        ");
        
        $siswaList = $querySiswa->getResult();
        $result = [];

        foreach($siswaList as $siswa) {
            $id = $siswa->id_siswa;
            $res = [
                'id_siswa' => $id,
                'nm_siswa' => $siswa->nm_siswa,
                'jam_masuk' => null,
                'jam_pulang' => null,
                'status' => 'Alpha'
            ];

            // Cek hadir
            $qHadir = $db->query("SELECT sts_hadir, jam FROM t_siswa_hadir WHERE id_siswa = '$id' AND tgl_hadir = '$tgl' ORDER BY sts_hadir ASC");
            $absens = $qHadir->getResult();
            if(count($absens) > 0) {
                foreach($absens as $absen) {
                    if($absen->sts_hadir == 0) {
                        $res['jam_masuk'] = substr($absen->jam, 0, 5);
                        $res['status'] = ($absen->jam > $jammasuk) ? 'Terlambat' : 'Hadir';
                    } else if($absen->sts_hadir == 1) {
                        $res['jam_pulang'] = substr($absen->jam, 0, 5);
                        if($res['status'] == 'Alpha') $res['status'] = 'Pulang'; 
                    }
                }
            } else {
                // Cek sakit/izin
                $qIzin = $db->query("SELECT sts_absen FROM t_siswa_absen WHERE id_siswa = '$id' AND tgl_absen = '$tgl'");
                $izin = $qIzin->getRow();
                if($izin) {
                    if($izin->sts_absen == 2) $res['status'] = 'Sakit';
                    else if($izin->sts_absen == 3) $res['status'] = 'Izin';
                }
            }
            $result[] = $res;
        }

        return $this->response->setJSON($result);
    }
}
