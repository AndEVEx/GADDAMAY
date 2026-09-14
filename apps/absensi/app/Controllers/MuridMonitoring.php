<?php

namespace App\Controllers;

use App\Models\MuridMonitoring_model;
use App\Models\Siswa_model;
use App\Models\Rombel_model;
use App\Models\Siswarombel_model;

use CodeIgniter\Controller;

date_default_timezone_set('Asia/Jakarta');

class MuridMonitoring extends Controller
{
    protected $monitoringModel;

    public function __construct()
    {
        $this->monitoringModel = new MuridMonitoring_model();
    }

    /**
     * Get the rombel IDs this guru is responsible for (as walikelas or BK)
     */
    private function getMyRombels()
    {
        $id_user = session()->get('id_user');
        $id_tapel = session()->get('id_tapel');

        $waliRombels = $this->monitoringModel->getRombelWalikelas($id_user, $id_tapel);
        $bkRombels = $this->monitoringModel->getRombelBK($id_user, $id_tapel);

        // Merge and deduplicate
        $allRombels = [];
        foreach ($waliRombels as $r) {
            $allRombels[$r['id_rombel']] = $r;
        }
        foreach ($bkRombels as $r) {
            $allRombels[$r['id_rombel']] = $r;
        }

        return $allRombels;
    }

    /**
     * Dashboard: daily attendance + monitoring table
     */
    public function index()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }

        $id_tapel = session()->get('id_tapel');
        $id_user = session()->get('id_user');
        $tgl = date('Y-m-d');

        echo view('func_siswa');

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Dashboard Monitoring Murid',
            'nav' => 'MuridMonitoring'
        );

        $myRombels = $this->getMyRombels();
        $rombelIds = array_keys($myRombels);

        // Get students in my rombels
        $db = \Config\Database::connect();
        $students = [];
        $monitorList = [];

        if (!empty($rombelIds)) {
            $students = $db->table('t_siswa_rombel sr')
                ->select('s.id_siswa, s.no_induk, s.nm_siswa, s.hp, r.nm_rombel, sr.id_rombel')
                ->join('t_siswa s', 's.id_siswa = sr.id_siswa')
                ->join('t_rombel r', 'r.id_rombel = sr.id_rombel')
                ->whereIn('sr.id_rombel', $rombelIds)
                ->where('sr.id_tapel', $id_tapel)
                ->where('s.sts_siswa', 1)
                ->orderBy('r.nm_rombel', 'ASC')
                ->orderBy('s.nm_siswa', 'ASC')
                ->get()->getResultArray();

            $monitorList = $this->monitoringModel->getMonitoringByRombels($rombelIds, $id_tapel);
        }

        // Get progress for each monitoring case
        foreach ($monitorList as &$m) {
            $m['progress'] = $this->monitoringModel->getProgress($m['id_monitoring']);
        }
        unset($m);

        // Count stats & build attendance data
        $totalSiswa = count($students);
        $hadirCount = 0;
        $terlambatCount = 0;
        $pulangList = [];
        foreach ($students as $s) {
            $check = $db->table('t_siswa_hadir')
                ->where('id_siswa', $s['id_siswa'])
                ->where('tgl_hadir', $tgl)
                ->where('sts_hadir', 0)
                ->countAllResults();
            if ($check > 0) {
                $hadirCount++;
                // Check terlambat
                $masukRow = $db->table('t_siswa_hadir')
                    ->select('jam')
                    ->where('id_siswa', $s['id_siswa'])
                    ->where('tgl_hadir', $tgl)
                    ->where('sts_hadir', 0)
                    ->get()->getRow();
                $hariIni = date('N');
                $jamMasukHari = $db->table('r_hari')->where('nm_hari', ['1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu'][$hariIni] ?? 'Senin')->get()->getRow();
                if ($masukRow && $jamMasukHari && $masukRow->jam > $jamMasukHari->jammasuk) {
                    $terlambatCount++;
                }
            }
            // Check pulang
            $pulangRow = $db->table('t_siswa_hadir')
                ->select('jam')
                ->where('id_siswa', $s['id_siswa'])
                ->where('tgl_hadir', $tgl)
                ->where('sts_hadir', 1)
                ->get()->getRow();
            if ($pulangRow) {
                $pulangList[] = [
                    'nm_siswa' => $s['nm_siswa'],
                    'no_induk' => $s['no_induk'],
                    'nm_rombel' => $s['nm_rombel'],
                    'jam_pulang' => $pulangRow->jam
                ];
            }
        }

        // Detect role for this guru
        $waliRombels = $this->monitoringModel->getRombelWalikelas($id_user, $id_tapel);
        $bkRombels = $this->monitoringModel->getRombelBK($id_user, $id_tapel);
        $isWalikelas = !empty($waliRombels);
        $isBK = !empty($bkRombels);

        $data = array(
            'students' => $students,
            'monitorList' => $monitorList,
            'myRombels' => $myRombels,
            'totalSiswa' => $totalSiswa,
            'hadirCount' => $hadirCount,
            'terlambatCount' => $terlambatCount,
            'tidakHadirCount' => $totalSiswa - $hadirCount,
            'pulangList' => $pulangList,
            'monitorCount' => count($monitorList),
            'tgl' => $tgl,
            'isWalikelas' => $isWalikelas,
            'isBK' => $isBK
        );

        echo view('index/sidebar');
        echo view('index/navbar', $datanav);
        echo view('home/home_walikelas_bk', $data);
        echo view('index/footer');
    }

    /**
     * Add student to monitoring
     */
    public function add()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }

        $id_siswa = $this->request->getPost('id_siswa');
        $id_rombel = $this->request->getPost('id_rombel');
        $alasan = $this->request->getPost('alasan');
        $id_tapel = session()->get('id_tapel');

        // Check if there's already an active case for this student
        $db = \Config\Database::connect();
        $existing = $db->table('t_murid_monitoring')
            ->where('id_siswa', $id_siswa)
            ->where('id_tapel', $id_tapel)
            ->where('status', 'active')
            ->countAllResults();

        if ($existing > 0) {
            session()->setFlashdata('error', 'Siswa sudah ada dalam monitoring aktif');
            return redirect()->to('MuridMonitoring');
        }

        $data = [
            'id_siswa' => $id_siswa,
            'id_rombel' => $id_rombel,
            'id_tapel' => $id_tapel,
            'alasan' => $alasan,
            'created_by' => session()->get('id_user')
        ];

        $this->monitoringModel->addMonitoring($data);

        session()->setFlashdata('success', 'Siswa ditambahkan ke monitoring');
        return redirect()->to('MuridMonitoring');
    }

    /**
     * View monitoring detail with progress buttons
     */
    public function action($id_monitoring)
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }

        echo view('func_siswa');

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Detail Monitoring',
            'nav' => 'MuridMonitoring'
        );

        $monitoring = $this->monitoringModel->getMonitoringById($id_monitoring);
        if (!$monitoring) {
            session()->setFlashdata('error', 'Data tidak ditemukan');
            return redirect()->to('MuridMonitoring');
        }

        $progress = $this->monitoringModel->getProgress($id_monitoring);

        $stepLabels = [
            1 => 'Murid dipanggil ke BK',
            2 => 'Orangtua dipanggil ke sekolah',
            3 => 'Homevisit',
            4 => 'Pengambilan keputusan'
        ];

        $data = array(
            'monitoring' => $monitoring,
            'progress' => $progress,
            'stepLabels' => $stepLabels
        );

        echo view('index/sidebar');
        echo view('index/navbar', $datanav);
        echo view('monitoring/monitoring_action', $data);
        echo view('index/footer');
    }

    /**
     * Complete a step with file upload
     */
    public function completeStep()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }

        $id_monitoring = $this->request->getPost('id_monitoring');
        $step = $this->request->getPost('step');
        $catatan = $this->request->getPost('catatan');

        // Handle file upload
        $file = $this->request->getFile('file_bukti');
        $fileName = null;
        $fileType = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getClientExtension());

            // Validate extension
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
                session()->setFlashdata('error', 'Format file harus JPG, PNG, atau PDF');
                return redirect()->to('MuridMonitoring/action/' . $id_monitoring);
            }

            // Validate size (5MB max)
            if ($file->getSizeByUnit('mb') > 5) {
                session()->setFlashdata('error', 'Ukuran file maksimal 5MB');
                return redirect()->to('MuridMonitoring/action/' . $id_monitoring);
            }

            $fileType = ($ext == 'pdf') ? 'pdf' : 'image';
            $fileName = $id_monitoring . '_' . $step . '_' . time() . '.' . $ext;

            $file->move(FCPATH . 'uploads/monitoring/', $fileName);
        } else {
            session()->setFlashdata('error', 'File bukti wajib diupload');
            return redirect()->to('MuridMonitoring/action/' . $id_monitoring);
        }

        $this->monitoringModel->completeStep(
            $id_monitoring,
            $step,
            $fileName,
            $fileType,
            session()->get('id_user'),
            $catatan
        );

        session()->setFlashdata('success', 'Langkah ' . $step . ' berhasil diselesaikan');
        return redirect()->to('MuridMonitoring/action/' . $id_monitoring);
    }

    /**
     * Mark case as resolved (Tandai Selesai)
     */
    public function resolve($id_monitoring)
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }

        $this->monitoringModel->resolveCase($id_monitoring);

        session()->setFlashdata('success', 'Kasus monitoring diselesaikan');
        return redirect()->to('MuridMonitoring');
    }

    /**
     * Reopen case at next step level
     */
    public function reopen($id_monitoring)
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }

        $alasan = $this->request->getPost('alasan');

        $oldCase = $this->monitoringModel->getMonitoringById($id_monitoring);
        if (!$oldCase) {
            session()->setFlashdata('error', 'Data tidak ditemukan');
            return redirect()->to('MuridMonitoring');
        }

        // Resolve old case first
        $this->monitoringModel->resolveCase($id_monitoring);

        // New case starts at next step (max 4)
        $nextStep = min($oldCase['current_step'] + 1, 4);

        $this->monitoringModel->reopenCase(
            $oldCase['id_siswa'],
            $oldCase['id_rombel'],
            $oldCase['id_tapel'],
            $alasan ?: 'Pelanggaran berulang - ' . $oldCase['alasan'],
            session()->get('id_user'),
            $nextStep
        );

        session()->setFlashdata('success', 'Siswa ditambahkan kembali ke monitoring di langkah ' . $nextStep);
        return redirect()->to('MuridMonitoring');
    }

    /**
     * Kepsek view: all active monitoring cases
     */
    public function kepsekView()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }

        $id_tapel = session()->get('id_tapel');
        $monitorList = $this->monitoringModel->getMonitoringAll($id_tapel);

        foreach ($monitorList as &$m) {
            $m['progress'] = $this->monitoringModel->getProgress($m['id_monitoring']);
        }
        unset($m);

        return $monitorList;
    }

    /**
     * Walikelas: Update student data (limited fields)
     */
    public function updateSiswa()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }

        $id_siswa = $this->request->getPost('id_siswa');
        $id_tapel = session()->get('id_tapel');
        $id_user = session()->get('id_user');

        // Verify this teacher is walikelas/BK for this student's rombel
        $db = \Config\Database::connect();
        $studentRombel = $db->table('t_siswa_rombel sr')
            ->select('sr.id_rombel')
            ->join('t_rombel r', 'r.id_rombel = sr.id_rombel')
            ->where('sr.id_siswa', $id_siswa)
            ->where('sr.id_tapel', $id_tapel)
            ->groupStart()
                ->where('r.id_walikelas', $id_user)
                ->orWhere('r.id_guru_bk', $id_user)
            ->groupEnd()
            ->get()->getRow();

        if (!$studentRombel) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses untuk mengedit siswa ini');
            return redirect()->to('MuridMonitoring');
        }

        $data = [
            'hp' => $this->request->getPost('hp'),
            'alamat' => $this->request->getPost('alamat'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tgl_lahir' => $this->request->getPost('tgl_lahir'),
        ];

        // Handle photo upload
        $file = $this->request->getFile('file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move(FCPATH . 'image/siswa/', $fileName);
            $data['file'] = $fileName;
        }

        $db->table('t_siswa')->where('id_siswa', $id_siswa)->update($data);

        session()->setFlashdata('success', 'Data siswa berhasil diupdate');
        return redirect()->to('MuridMonitoring');
    }

    public function bulkUpdateHadir()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }

        $ids = $this->request->getPost('ids');
        $status = $this->request->getPost('status');
        $jam = $this->request->getPost('jam') ?: date('H:i');
        $tgl = $this->request->getPost('tgl') ?: date('Y-m-d');
        $keterangan = $this->request->getPost('keterangan') ?: '';

        if ($tgl > date('Y-m-d')) {
            session()->setFlashdata('error', 'Tanggal koreksi tidak boleh melebihi hari ini.');
            return redirect()->back();
        }

        if (empty($ids) || !is_array($ids)) {
            session()->setFlashdata('error', 'Tidak ada siswa yang dipilih');
            return redirect()->back();
        }

        $db = \Config\Database::connect();
        $id_tapel = session()->get('id_tapel');
        
        $db->transStart();

        if (in_array($status, ['Masuk', 'Terlambat', 'Pulang'])) {
            $sts_hadir = ($status == 'Pulang') ? 1 : 0;
            
            // Delete conflicting ketidakhadiran
            $db->table('t_siswa_absen')
               ->where('tgl_absen', $tgl)
               ->whereIn('id_siswa', $ids)
               ->delete();

            $existing = $db->table('t_siswa_hadir')
                ->select('id_siswa_hadir, id_siswa')
                ->where('tgl_hadir', $tgl)
                ->where('sts_hadir', $sts_hadir)
                ->whereIn('id_siswa', $ids)
                ->get()->getResultArray();
            
            $existingMap = [];
            foreach ($existing as $e) {
                $existingMap[$e['id_siswa']] = $e['id_siswa_hadir'];
            }

            $updateData = [];
            $insertData = [];

            foreach ($ids as $id_siswa) {
                if (isset($existingMap[$id_siswa])) {
                    $updateData[] = [
                        'id_siswa_hadir' => $existingMap[$id_siswa],
                        'jam' => $jam
                    ];
                } else {
                    $insertData[] = [
                        'id_siswa' => $id_siswa,
                        'tgl_hadir' => $tgl,
                        'sts_hadir' => $sts_hadir,
                        'jam' => $jam,
                        'id_tapel' => $id_tapel
                    ];
                }
            }

            if (!empty($updateData)) {
                $db->table('t_siswa_hadir')->updateBatch($updateData, 'id_siswa_hadir');
            }
            if (!empty($insertData)) {
                $db->table('t_siswa_hadir')->insertBatch($insertData);
            }

        } else {
            $sts_absen = ($status == 'Sakit') ? 2 : (($status == 'Izin') ? 3 : 4);
            
            // Delete conflicting kehadiran
            $db->table('t_siswa_hadir')
               ->where('tgl_hadir', $tgl)
               ->whereIn('id_siswa', $ids)
               ->delete();

            $existing = $db->table('t_siswa_absen')
                ->select('id_siswa_absen, id_siswa')
                ->where('tgl_absen', $tgl)
                ->whereIn('id_siswa', $ids)
                ->get()->getResultArray();

            $existingMap = [];
            foreach ($existing as $e) {
                $existingMap[$e['id_siswa']] = $e['id_siswa_absen'];
            }

            $updateData = [];
            $insertData = [];

            foreach ($ids as $id_siswa) {
                if (isset($existingMap[$id_siswa])) {
                    $updateData[] = [
                        'id_siswa_absen' => $existingMap[$id_siswa],
                        'sts_absen' => $sts_absen,
                        'ket_absen' => $keterangan ?: 'Koreksi Masal Rombel',
                        'sts_approve' => 1
                    ];
                } else {
                    $insertData[] = [
                        'id_siswa' => $id_siswa,
                        'tgl_absen' => $tgl,
                        'sts_absen' => $sts_absen,
                        'ket_absen' => $keterangan ?: 'Koreksi Masal Rombel',
                        'id_tapel' => $id_tapel,
                        'tgl_entri' => date('Y-m-d H:i:s'),
                        'sts_approve' => 1
                    ];
                }
            }

            if (!empty($updateData)) {
                $db->table('t_siswa_absen')->updateBatch($updateData, 'id_siswa_absen');
            }
            if (!empty($insertData)) {
                $db->table('t_siswa_absen')->insertBatch($insertData);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat memproses koreksi masal.');
        } else {
            session()->setFlashdata('success', 'Koreksi masal berhasil dilakukan');
        }
        
        return redirect()->back();
    }
}
