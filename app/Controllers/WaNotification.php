<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\WaMessageQueue_model;

date_default_timezone_set('Asia/Jakarta');

class WaNotification extends Controller
{
    protected $waQueue;
    protected $db;

    public function __construct()
    {
        $this->waQueue = new WaMessageQueue_model();
        $this->db = \Config\Database::connect();
    }

    /**
     * Main Dashboard - Queue Dashboard
     */
    public function index()
    {
        // Ensure settings table exists before any access
        $this->ensureSettingsTable();

        // Get statistics
        $stats = $this->getStats();

        // Get recent messages
        $recentPending = $this->waQueue
            ->where('status', 'pending')
            ->orderBy('scheduled_date', 'ASC')
            ->limit(10)
            ->findAll();

        $recentSent = $this->waQueue
            ->where('status', 'sent')
            ->orderBy('sent_at', 'DESC')
            ->limit(10)
            ->findAll();

        $recentFailed = $this->waQueue
            ->where('status', 'failed')
            ->orderBy('updated_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get schedule distribution (messages per day)
        $scheduleDistribution = $this->db->query("
            SELECT scheduled_date, COUNT(*) as count, status
            FROM wa_message_queue 
            WHERE scheduled_date >= CURDATE()
            GROUP BY scheduled_date, status
            ORDER BY scheduled_date ASC
            LIMIT 14
        ")->getResultArray();

        // Get message template from settings or use default
        $templateRow = $this->db->table('wa_settings')->where('key', 'message_template')->get()->getRow();
        $messageTemplate = $templateRow->value ?? $this->getDefaultTemplate();

        // Get gateway settings
        $gatewaySettings = $this->getGatewaySettings();

        $datanav = [
            'nama' => session()->get('nama'),
            'title' => 'WhatsApp Notification',
            'nav' => 'Dashboard'
        ];

        $data = [
            'title' => 'WhatsApp Notification',
            'nav' => 'Dashboard',
            'stats' => $stats,
            'recentPending' => $recentPending,
            'recentSent' => $recentSent,
            'recentFailed' => $recentFailed,
            'scheduleDistribution' => $scheduleDistribution,
            'messageTemplate' => $messageTemplate,
            'gatewaySettings' => $gatewaySettings,
        ];

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar', $datanav);
        echo view('wa_notification/dashboard', $data);
        echo view('index/footer');
    }

    /**
     * Get queue statistics
     */
    private function getStats()
    {
        $pending = $this->waQueue->where('status', 'pending')->countAllResults();
        $processing = $this->waQueue->where('status', 'processing')->countAllResults();
        $sent = $this->waQueue->where('status', 'sent')->countAllResults();
        $failed = $this->waQueue->where('status', 'failed')->countAllResults();
        $today = $this->waQueue
            ->where('scheduled_date', date('Y-m-d'))
            ->where('status', 'pending')
            ->countAllResults();
        $todaySent = $this->waQueue
            ->where('DATE(sent_at)', date('Y-m-d'))
            ->where('status', 'sent')
            ->countAllResults();

        return [
            'pending' => $pending,
            'processing' => $processing,
            'sent' => $sent,
            'failed' => $failed,
            'today' => $today,
            'todaySent' => $todaySent,
            'total' => $pending + $processing + $sent + $failed,
        ];
    }

    /**
     * Get gateway settings
     */
    private function getGatewaySettings()
    {
        // Create settings table if not exists
        $this->ensureSettingsTable();

        $settings = [];
        $rows = $this->db->table('wa_settings')->get()->getResultArray();
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }

        return array_merge([
            'gateway_url' => '',
            'gateway_token' => '',
            'sender_number' => '',
            'message_delay' => '30',
            'distribution_days' => '7',
            'schedule_day' => 'Friday',
            'schedule_time' => '16:00',
            'gateway_status' => 'disconnected',
        ], $settings);
    }

    /**
     * Ensure settings table exists
     */
    private function ensureSettingsTable()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS wa_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                `key` VARCHAR(100) UNIQUE,
                value TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Get default message template
     */
    private function getDefaultTemplate()
    {
        return "📊 *LAPORAN KEHADIRAN MINGGUAN*\n🏫 SMKN 2 INDRAMAYU\n\nYth. Orang Tua/Wali dari:\n👤 *{nama_siswa}*\n🆔 NIS: {nis}\n📚 Kelas: *{kelas}*\n\n📅 Periode: {periode}\n\n{attendance_list}\n\n⚠️ = Terlambat\n❌ = Tidak Hadir\n\nTerima kasih atas perhatian Bapak/Ibu 🙏\n— *Sistem Absensi Digital*\n*SMKN 2 INDRAMAYU*";
    }

    /**
     * View all queue messages
     */
    public function queue()
    {
        $status = $this->request->getGet('status') ?? 'all';
        $date = $this->request->getGet('date') ?? '';

        $builder = $this->db->table('wa_message_queue q')
            ->select('q.*, s.nm_siswa, s.no_induk')
            ->join('t_siswa s', 's.id_siswa = q.id_siswa', 'left');

        if ($status !== 'all') {
            $builder->where('q.status', $status);
        }

        if (!empty($date)) {
            $builder->where('q.scheduled_date', $date);
        }

        $messages = $builder->orderBy('q.scheduled_date', 'ASC')
            ->orderBy('q.created_at', 'ASC')
            ->get()
            ->getResultArray();

        $datanav = [
            'nama' => session()->get('nama'),
            'title' => 'Antrian Pesan WhatsApp',
            'nav' => 'Queue'
        ];

        $data = [
            'title' => 'Antrian Pesan WhatsApp',
            'nav' => 'Queue',
            'messages' => $messages,
            'currentStatus' => $status,
            'currentDate' => $date,
            'stats' => $this->getStats(),
        ];

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar', $datanav);
        echo view('wa_notification/queue', $data);
        echo view('index/footer');
    }

    /**
     * View broadcast schedule
     */
    public function schedule()
    {
        $schedule = $this->db->query("
            SELECT 
                scheduled_date,
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                MIN(week_start) as week_start,
                MAX(week_end) as week_end
            FROM wa_message_queue
            GROUP BY scheduled_date
            ORDER BY scheduled_date DESC
            LIMIT 30
        ")->getResultArray();

        $datanav = [
            'nama' => session()->get('nama'),
            'title' => 'Jadwal Broadcast',
            'nav' => 'Schedule'
        ];

        $data = [
            'title' => 'Jadwal Broadcast',
            'nav' => 'Schedule',
            'schedule' => $schedule,
        ];

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar', $datanav);
        echo view('wa_notification/schedule', $data);
        echo view('index/footer');
    }

    /**
     * Preview message for student
     */
    public function preview($id_siswa = null)
    {
        if (!$id_siswa) {
            return redirect()->to('/WaNotification')->with('error', 'ID siswa tidak ditemukan');
        }

        // Get student data
        $tapelRow = $this->db->table('r_tapel')->where('sts_aktif', 1)->get()->getRow();
        $id_tapel = $tapelRow->id_tapel ?? null;

        $student = $this->db->table('t_siswa s')
            ->select('s.id_siswa, s.nm_siswa, s.no_induk, s.nisn, s.hp, r.nm_rombel')
            ->join('t_siswa_rombel sr', 'sr.id_siswa = s.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = sr.id_rombel')
            ->where('s.id_siswa', $id_siswa)
            ->where('sr.id_tapel', $id_tapel)
            ->get()
            ->getRowArray();

        if (!$student) {
            return redirect()->to('/WaNotification')->with('error', 'Siswa tidak ditemukan');
        }

        // Calculate week range
        $friday = date('Y-m-d', strtotime('last friday'));
        $monday = date('Y-m-d', strtotime('last monday', strtotime($friday)));

        // Get weekly attendance
        $attendance = $this->getWeeklyAttendance($id_siswa, $monday, $friday);
        $message = $this->formatWeeklyMessage($student, $attendance, $monday, $friday);

        $datanav = [
            'nama' => session()->get('nama'),
            'title' => 'Preview Pesan',
            'nav' => 'Preview'
        ];

        $data = [
            'title' => 'Preview Pesan',
            'nav' => 'Preview',
            'student' => $student,
            'attendance' => $attendance,
            'message' => $message,
            'monday' => $monday,
            'friday' => $friday,
        ];

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar', $datanav);
        echo view('wa_notification/preview', $data);
        echo view('index/footer');
    }

    /**
     * Settings page
     */
    public function settings()
    {
        $gatewaySettings = $this->getGatewaySettings();

        $datanav = [
            'nama' => session()->get('nama'),
            'title' => 'Pengaturan WhatsApp',
            'nav' => 'Settings'
        ];

        $data = [
            'title' => 'Pengaturan WhatsApp',
            'nav' => 'Settings',
            'settings' => $gatewaySettings,
            'messageTemplate' => $gatewaySettings['message_template'] ?? $this->getDefaultTemplate(),
        ];

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar', $datanav);
        echo view('wa_notification/settings', $data);
        echo view('index/footer');
    }

    /**
     * Save settings
     */
    public function saveSettings()
    {
        $this->ensureSettingsTable();

        $fields = [
            'gateway_url',
            'basic_auth_user',
            'basic_auth_pass',
            'sender_number_1',
            'sender_number_2',
            'device_id_1',
            'device_id_2',
            'message_delay',
            'distribution_days',
            'schedule_day',
            'schedule_time',
            'message_template',
            'channel_jid_1',
            'channel_jid_2'
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
        return redirect()->to('/WaNotification/settings')->with('success', 'Pengaturan berhasil disimpan');
    }

    /**
     * Test gateway connection (AJAX)
     */
    public function testGateway()
    {
        $waGateway = new \App\Libraries\WaGatewayService();

        $status1 = $waGateway->checkStatus(0);
        $status2 = $waGateway->checkStatus(1);

        // Save connection status to wa_settings
        $isConnected = ($status1['connected'] ?? false) && ($status1['logged_in'] ?? false);
        $statusValue = $isConnected ? 'connected' : 'disconnected';
        $this->db->query("
            INSERT INTO wa_settings (`key`, value) VALUES ('gateway_status', ?)
            ON DUPLICATE KEY UPDATE value = ?
        ", [$statusValue, $statusValue]);

        return $this->response->setJSON([
            'success' => true,
            'gateway_url' => $waGateway->getBaseUrl(),
            'gateway_connected' => $isConnected,
            'sender_1' => [
                'connected' => $status1['connected'] ?? false,
                'logged_in' => $status1['logged_in'] ?? false,
                'device_id' => $status1['device_id'] ?? null,
                'error' => $status1['error'] ?? null,
            ],
            'sender_2' => [
                'connected' => $status2['connected'] ?? false,
                'logged_in' => $status2['logged_in'] ?? false,
                'device_id' => $status2['device_id'] ?? null,
                'error' => $status2['error'] ?? null,
            ],
        ]);
    }

    /**
     * Resend failed messages
     */
    public function resendFailed()
    {
        $updated = $this->waQueue
            ->where('status', 'failed')
            ->where('retry_count <', 3)
            ->set('status', 'pending')
            ->update();

        $count = $this->db->affectedRows();

        return redirect()->to('/WaNotification')->with('success', "$count pesan gagal dijadwalkan ulang");
    }

    /**
     * Delete message from queue
     */
    public function deleteMessage($id)
    {
        $this->waQueue->delete($id);
        return redirect()->to('/WaNotification/queue')->with('success', 'Pesan berhasil dihapus');
    }

    /**
     * Generate weekly report (trigger manually)
     */
    public function generate()
    {
        // Redirect to WeeklyReport controller
        return redirect()->to('/WeeklyReport/generate');
    }

    /**
     * Search students for preview
     */
    public function searchStudent()
    {
        $keyword = $this->request->getGet('q') ?? '';

        if (strlen($keyword) < 2) {
            return $this->response->setJSON([]);
        }

        $tapelRow = $this->db->table('r_tapel')->where('sts_aktif', 1)->get()->getRow();
        $id_tapel = $tapelRow->id_tapel ?? null;

        $students = $this->db->table('t_siswa s')
            ->select('s.id_siswa, s.nm_siswa, s.no_induk, r.nm_rombel')
            ->join('t_siswa_rombel sr', 'sr.id_siswa = s.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = sr.id_rombel')
            ->where('sr.id_tapel', $id_tapel)
            ->groupStart()
            ->like('s.nm_siswa', $keyword)
            ->orLike('s.no_induk', $keyword)
            ->groupEnd()
            ->limit(10)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($students);
    }

    /**
     * Get weekly attendance for a student
     */
    private function getWeeklyAttendance($id_siswa, $monday, $friday)
    {
        $attendance = [];
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $records = $this->db->table('t_siswa_hadir')
            ->where('id_siswa', $id_siswa)
            ->where('tgl_hadir >=', $monday)
            ->where('tgl_hadir <=', $friday)
            ->orderBy('tgl_hadir', 'ASC')
            ->orderBy('sts_hadir', 'ASC')
            ->get()
            ->getResultArray();

        $schedules = $this->db->table('r_hari')->get()->getResultArray();
        $scheduleMap = [];
        foreach ($schedules as $sch) {
            $scheduleMap[$sch['nm_hari']] = $sch;
        }

        $currentDate = $monday;
        foreach ($days as $dayName) {
            $attendance[$currentDate] = [
                'hari' => $dayName,
                'tanggal' => $currentDate,
                'masuk' => null,
                'pulang' => null,
                'terlambat' => false,
            ];
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        foreach ($records as $record) {
            $tgl = $record['tgl_hadir'];
            if (!isset($attendance[$tgl]))
                continue;

            if ($record['sts_hadir'] == 0) {
                $attendance[$tgl]['masuk'] = substr($record['jam'], 0, 5);
                $dayName = $attendance[$tgl]['hari'];
                $jamMasuk = $scheduleMap[$dayName]['jammasuk'] ?? '07:00:00';
                if ($record['jam'] > $jamMasuk) {
                    $attendance[$tgl]['terlambat'] = true;
                }
            } else {
                $attendance[$tgl]['pulang'] = substr($record['jam'], 0, 5);
            }
        }

        return $attendance;
    }

    /**
     * Format weekly message
     */
    private function formatWeeklyMessage($student, $attendance, $monday, $friday)
    {
        $bulan = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des'
        ];

        $formatDate = function ($date) use ($bulan) {
            return date('j', strtotime($date)) . ' ' . $bulan[(int) date('n', strtotime($date))] . ' ' . date('Y', strtotime($date));
        };

        $message = "📊 *LAPORAN KEHADIRAN MINGGUAN*\n";
        $message .= "🏫 SMKN 2 INDRAMAYU\n\n";
        $message .= "Yth. Orang Tua/Wali dari:\n";
        $message .= "👤 *{$student['nm_siswa']}*\n";
        $message .= "🆔 NIS: {$student['no_induk']}\n";
        $message .= "📚 Kelas: *{$student['nm_rombel']}*\n\n";
        $message .= "📅 Periode: {$formatDate($monday)} - {$formatDate($friday)}\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";

        foreach ($attendance as $data) {
            $hari = $data['hari'];
            $masuk = $data['masuk'] ?? '❌';
            $pulang = $data['pulang'] ?? '-';
            $terlambat = $data['terlambat'] ? ' ⚠️' : '';

            if ($masuk === '❌') {
                $message .= "📌 *{$hari}*: ❌ Tidak Hadir\n";
            } else {
                $message .= "📌 *{$hari}*: {$masuk}{$terlambat} ➜ {$pulang}\n";
            }
        }

        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
        $message .= "⚠️ = Terlambat\n❌ = Tidak Hadir\n\n";
        $message .= "Terima kasih 🙏\n— *SMKN 2 INDRAMAYU*";

        return $message;
    }

    /**
     * API: Get queue stats (for AJAX)
     */
    public function apiStats()
    {
        return $this->response->setJSON($this->getStats());
    }
}
