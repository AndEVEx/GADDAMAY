<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\AttendanceAlert_model;
use App\Models\WaMessageQueue_model;

date_default_timezone_set('Asia/Jakarta');

/**
 * AttendanceAlert Controller
 * 
 * Manages attendance alert detection, display, and notifications
 */
class AttendanceAlert extends Controller
{
    protected $alertModel;
    protected $waQueue;
    protected $db;

    public function __construct()
    {
        $this->alertModel = new AttendanceAlert_model();
        $this->waQueue = new WaMessageQueue_model();
        $this->db = \Config\Database::connect();
    }

    /**
     * Dashboard - List all active alerts
     */
    public function index()
    {
        $alertType = $this->request->getGet('type');
        $status = $this->request->getGet('status');

        $data = [
            'title' => 'Peringatan Kehadiran',
            'alerts' => $this->alertModel->getActiveAlerts($alertType, $status),
            'stats' => $this->alertModel->getAlertStats(),
            'settings' => $this->getSettings(),
            'rombels' => $this->getRombels(),
            'currentType' => $alertType,
            'currentStatus' => $status,
        ];

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar', [
            'nama' => session()->get('nama'),
            'title' => 'Peringatan Kehadiran',
            'nav' => 'Peringatan'
        ]);
        echo view('attendance/alerts', $data);
        echo view('index/footer');
    }

    /**
     * Report page with filters
     */
    public function report()
    {
        $filters = [
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'alert_type' => $this->request->getGet('type'),
            'id_rombel' => $this->request->getGet('rombel'),
        ];

        $data = [
            'title' => 'Laporan Peringatan Kehadiran',
            'alerts' => $this->alertModel->getAlertHistory($filters),
            'stats' => $this->alertModel->getAlertStats(),
            'rombels' => $this->getRombels(),
            'filters' => $filters,
        ];

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar', [
            'nama' => session()->get('nama'),
            'title' => 'Laporan Peringatan Kehadiran',
            'nav' => 'Laporan'
        ]);
        echo view('attendance/report', $data);
        echo view('index/footer');
    }

    /**
     * Scan for new alerts - run daily via cron or after weekly report
     */
    public function scan()
    {
        // Cek apakah notifikasi sedang di-pause
        $paused = $this->getSetting('notification_paused');
        if ($paused === '1') {
            $reason = $this->getSetting('notification_pause_reason') ?: 'Tidak ada keterangan';
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notifikasi sedang dijeda. Alasan: ' . $reason,
                'paused' => true,
            ]);
        }

        $results = [
            'consecutive' => [],
            'weekly' => [],
            'monthly' => [],
            'created' => 0,
        ];

        $today = date('Y-m-d');

        // 1. Check consecutive absences (2+ days)
        $consecutive = $this->alertModel->findConsecutiveAbsences($today);
        foreach ($consecutive as $student) {
            $alertData = [
                'id_siswa' => $student['id_siswa'],
                'id_rombel' => $student['id_rombel'],
                'alert_type' => 'consecutive',
                'alert_date' => $today,
                'absence_count' => 2,
                'absence_dates' => $student['absence_dates'],
                'status' => 'new',
            ];

            if ($this->alertModel->createAlert($alertData)) {
                $results['consecutive'][] = $student;
                $results['created']++;
            }
        }

        // 2. Check weekly absences (3+ times) - on Friday
        if (date('N') == 5) { // Friday
            $weekStart = date('Y-m-d', strtotime('monday this week'));
            $weekEnd = $today;

            $weekly = $this->alertModel->findWeeklyAbsences($weekStart, $weekEnd);
            foreach ($weekly as $student) {
                $alertData = [
                    'id_siswa' => $student['id_siswa'],
                    'id_rombel' => $student['id_rombel'],
                    'alert_type' => 'weekly',
                    'alert_date' => $today,
                    'absence_count' => $student['absent_count'],
                    'absence_dates' => $student['absence_dates'],
                    'status' => 'new',
                ];

                if ($this->alertModel->createAlert($alertData)) {
                    $results['weekly'][] = $student;
                    $results['created']++;
                }
            }
        }

        // 3. Check monthly absences (10+ days) - on last day of month
        if (date('d') == date('t')) { // Last day of month
            $monthly = $this->alertModel->findMonthlyAbsences();
            foreach ($monthly as $student) {
                $alertData = [
                    'id_siswa' => $student['id_siswa'],
                    'id_rombel' => $student['id_rombel'],
                    'alert_type' => 'monthly',
                    'alert_date' => $today,
                    'absence_count' => $student['absent_count'],
                    'absence_dates' => null,
                    'status' => 'new',
                ];

                if ($this->alertModel->createAlert($alertData)) {
                    $results['monthly'][] = $student;
                    $results['created']++;
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Scan selesai. {$results['created']} peringatan baru dibuat.",
            'results' => $results,
        ]);
    }

    /**
     * Scan and auto-send notifications (called after weekly report)
     */
    /**
     * Toggle pause notification
     */
    public function togglePause()
    {
        $currentPaused = $this->getSetting('notification_paused');
        $newValue = ($currentPaused === '1') ? '0' : '1';
        $reason = $this->request->getPost('reason') ?? '';

        $this->db->query("
            INSERT INTO wa_settings (`key`, value) VALUES ('notification_paused', ?)
            ON DUPLICATE KEY UPDATE value = ?
        ", [$newValue, $newValue]);

        $this->db->query("
            INSERT INTO wa_settings (`key`, value) VALUES ('notification_pause_reason', ?)
            ON DUPLICATE KEY UPDATE value = ?
        ", [$reason, $reason]);

        $statusText = ($newValue === '1') ? 'dijeda' : 'diaktifkan kembali';
        session()->setFlashdata('success', 'Notifikasi berhasil ' . $statusText);
        return redirect()->to('/AttendanceAlert');
    }

    public function scanAndNotify()
    {
        // Cek apakah notifikasi sedang di-pause
        $paused = $this->getSetting('notification_paused');
        if ($paused === '1') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notifikasi sedang dijeda',
                'paused' => true,
            ]);
        }

        // First scan for new alerts
        $scanResult = $this->scan();

        // Then send notifications for all new alerts
        $newAlerts = $this->alertModel->getActiveAlerts(null, 'new');
        $sent = [
            'walikelas' => 0,
            'bk' => 0,
        ];

        foreach ($newAlerts as $alert) {
            // Send to wali kelas
            if ($this->sendWalikelasNotification($alert['id'])) {
                $sent['walikelas']++;
            }

            // Send to guru BK
            if ($this->sendBKNotification($alert['id'])) {
                $sent['bk']++;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Notifikasi terkirim: {$sent['walikelas']} ke wali kelas, {$sent['bk']} ke guru BK",
            'sent' => $sent,
        ]);
    }

    /**
     * Send notification to wali kelas for a specific alert
     */
    public function notifyWalikelas($id)
    {
        $result = $this->sendWalikelasNotification($id);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notifikasi berhasil dikirim ke wali kelas',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengirim notifikasi',
        ]);
    }

    /**
     * Send notification to guru BK for a specific alert
     */
    public function notifyBK($id)
    {
        $result = $this->sendBKNotification($id);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notifikasi berhasil dikirim ke guru BK',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengirim notifikasi (guru BK mungkin belum diatur)',
        ]);
    }

    /**
     * Resolve an alert
     */
    public function resolve($id)
    {
        $notes = $this->request->getPost('notes');
        $userId = session()->get('id_user');

        $this->alertModel->markResolved($id, $userId, $notes);

        return redirect()->to('/AttendanceAlert')->with('success', 'Peringatan telah diselesaikan');
    }

    /**
     * Settings page for alert templates
     */
    public function settings()
    {
        $data = [
            'title' => 'Pengaturan Peringatan Kehadiran',
            'settings' => $this->getSettings(),
            'rombels' => $this->getRombelsWithTeachers(),
            'teachers' => $this->getTeachers(),
        ];

        echo view('func');
        echo view('index/sidebar', $data);
        echo view('attendance/settings', $data);
        echo view('index/footer');
    }

    /**
     * Save settings
     */
    public function saveSettings()
    {
        $fields = [
            'alert_template_walikelas',
            'alert_template_bk',
        ];

        foreach ($fields as $field) {
            $value = $this->request->getPost($field);
            if ($value !== null) {
                $this->db->query("
                    INSERT INTO wa_settings (`key`, value) VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE value = ?
                ", [$field, $value, $value]);
            }
        }

        return redirect()->to('/AttendanceAlert/settings')->with('success', 'Template berhasil disimpan');
    }

    /**
     * Save guru BK assignment for a rombel
     */
    public function saveGuruBK()
    {
        $idRombel = $this->request->getPost('id_rombel');
        $idGuruBK = $this->request->getPost('id_guru_bk');

        $this->db->table('t_rombel')
            ->where('id_rombel', $idRombel)
            ->update(['id_guru_bk' => $idGuruBK ?: null]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Guru BK berhasil disimpan',
        ]);
    }

    /**
     * API: Get stats for dashboard widgets
     */
    public function apiStats()
    {
        return $this->response->setJSON($this->alertModel->getAlertStats());
    }

    // ========== Private Methods ==========

    /**
     * Send notification to wali kelas
     */
    private function sendWalikelasNotification($alertId)
    {
        $alert = $this->getAlertWithDetails($alertId);
        if (!$alert || empty($alert['wali_kelas_hp'])) {
            return false;
        }

        $template = $this->getSetting('alert_template_walikelas');
        $message = $this->formatMessage($template, $alert);

        // Queue the message
        $this->waQueue->insert([
            'phone_number' => $this->normalizePhone($alert['wali_kelas_hp']),
            'recipient_name' => $alert['wali_kelas_nama'],
            'message' => $message,
            'message_type' => 'alert_walikelas',
            'status' => 'pending',
            'scheduled_date' => date('Y-m-d'),
        ]);

        $this->alertModel->markNotifiedWalikelas($alertId);
        return true;
    }

    /**
     * Send notification to guru BK
     */
    private function sendBKNotification($alertId)
    {
        $alert = $this->getAlertWithDetails($alertId);
        if (!$alert || empty($alert['guru_bk_hp'])) {
            return false;
        }

        $template = $this->getSetting('alert_template_bk');
        $message = $this->formatMessage($template, $alert);

        // Queue the message
        $this->waQueue->insert([
            'phone_number' => $this->normalizePhone($alert['guru_bk_hp']),
            'recipient_name' => $alert['guru_bk_nama'],
            'message' => $message,
            'message_type' => 'alert_bk',
            'status' => 'pending',
            'scheduled_date' => date('Y-m-d'),
        ]);

        $this->alertModel->markNotifiedBK($alertId);
        return true;
    }

    /**
     * Get alert with all details needed for notification
     */
    private function getAlertWithDetails($id)
    {
        return $this->db->table('attendance_alerts a')
            ->select('a.*, s.nm_siswa, s.no_induk, r.nm_rombel,
                      ptk.nama_ptk as wali_kelas_nama, ptk.no_hp as wali_kelas_hp,
                      bk.nama_ptk as guru_bk_nama, bk.no_hp as guru_bk_hp')
            ->join('t_siswa s', 's.id_siswa = a.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = a.id_rombel')
            ->join('t_ptk ptk', 'ptk.id_ptk = r.id_walikelas', 'left')
            ->join('t_ptk bk', 'bk.id_ptk = r.id_guru_bk', 'left')
            ->where('a.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Format message with placeholders
     */
    private function formatMessage($template, $alert)
    {
        $alertDescription = $this->getAlertDescription($alert);

        $replacements = [
            '{nama_siswa}' => $alert['nm_siswa'],
            '{nis}' => $alert['no_induk'],
            '{kelas}' => $alert['nm_rombel'],
            '{wali_kelas}' => $alert['wali_kelas_nama'] ?? '-',
            '{alert_description}' => $alertDescription,
            '{tanggal}' => date('d/m/Y'),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Get human-readable alert description
     */
    private function getAlertDescription($alert)
    {
        switch ($alert['alert_type']) {
            case 'consecutive':
                return "⚠️ Siswa tidak hadir 2 hari berturut-turut.\nTanggal: " . $alert['absence_dates'];

            case 'weekly':
                return "⚠️ Siswa tidak hadir " . $alert['absence_count'] . " kali dalam minggu ini.\nTanggal: " . $alert['absence_dates'];

            case 'monthly':
                return "🚨 Siswa tidak hadir lebih dari 10 hari dalam bulan ini.\nTotal tidak hadir: " . $alert['absence_count'] . " hari";

            default:
                return "Siswa memerlukan perhatian khusus terkait kehadiran.";
        }
    }

    /**
     * Normalize phone number to 62xxx format
     */
    private function normalizePhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }

    /**
     * Get all settings
     */
    private function getSettings()
    {
        $settings = [];
        $result = $this->db->table('wa_settings')->get()->getResultArray();
        foreach ($result as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }

    /**
     * Get a specific setting
     */
    private function getSetting($key)
    {
        $row = $this->db->table('wa_settings')->where('key', $key)->get()->getRowArray();
        return $row['value'] ?? '';
    }

    /**
     * Get all rombels
     */
    private function getRombels()
    {
        $tapel = session()->get('id_tapel');
        return $this->db->table('t_rombel')
            ->where('id_tapel', $tapel)
            ->orderBy('nm_rombel')
            ->get()
            ->getResultArray();
    }

    /**
     * Get rombels with teacher assignments
     */
    private function getRombelsWithTeachers()
    {
        $tapel = session()->get('id_tapel');
        return $this->db->table('t_rombel r')
            ->select('r.*, ptk.nama_ptk as wali_kelas_nama, bk.nama_ptk as guru_bk_nama')
            ->join('t_ptk ptk', 'ptk.id_ptk = r.id_walikelas', 'left')
            ->join('t_ptk bk', 'bk.id_ptk = r.id_guru_bk', 'left')
            ->where('r.id_tapel', $tapel)
            ->orderBy('r.nm_rombel')
            ->get()
            ->getResultArray();
    }

    /**
     * Get all active teachers for dropdown
     */
    private function getTeachers()
    {
        return $this->db->table('t_ptk')
            ->where('status_ptk', 1)
            ->orderBy('nama_ptk')
            ->get()
            ->getResultArray();
    }
}
