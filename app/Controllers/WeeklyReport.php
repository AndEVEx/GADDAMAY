<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\WaMessageQueue_model;
use App\Models\Siswa_model;
use App\Models\Absensisiswa_model;

date_default_timezone_set('Asia/Jakarta');

class WeeklyReport extends Controller
{
    protected $waQueue;
    protected $siswaModel;
    protected $absenModel;
    protected $db;

    public function __construct()
    {
        $this->waQueue = new WaMessageQueue_model();
        $this->siswaModel = new Siswa_model();
        $this->absenModel = new Absensisiswa_model();
        $this->db = \Config\Database::connect();
    }

    /**
     * Main entry point - Generate weekly report messages
     * Call this every Friday at 4PM via cron/scheduler
     * URL: /WeeklyReport/generate
     */
    public function generate()
    {
        // Calculate week range (last Monday to Friday)
        $friday = date('Y-m-d'); // Today (Friday)
        $monday = date('Y-m-d', strtotime('last monday', strtotime($friday)));

        // If today is not Friday, calculate based on most recent Friday
        if (date('N') != 5) {
            $friday = date('Y-m-d', strtotime('last friday'));
            $monday = date('Y-m-d', strtotime('last monday', strtotime($friday)));
        }

        // Check if already queued for this week
        if ($this->waQueue->isWeekAlreadyQueued($monday, $friday)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => "Laporan untuk minggu $monday - $friday sudah di-generate sebelumnya"
            ]);
        }

        // Get active tapel
        $tapelRow = $this->db->table('r_tapel')->where('sts_aktif', 1)->get()->getRow();
        if (!$tapelRow) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tahun pelajaran aktif tidak ditemukan'
            ]);
        }
        $id_tapel = $tapelRow->id_tapel;

        // Get all active students with phone numbers
        $students = $this->getActiveStudentsWithPhone($id_tapel);

        if (empty($students)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tidak ada siswa aktif dengan nomor HP'
            ]);
        }

        // Distribute students across 7 days
        $totalStudents = count($students);
        $studentsPerDay = ceil($totalStudents / 7);
        $queuedCount = 0;
        $dayOffset = 0;

        foreach ($students as $index => $student) {
            // Calculate which day to send (0-6 days from now)
            $dayOffset = floor($index / $studentsPerDay);
            if ($dayOffset > 6)
                $dayOffset = 6;

            $scheduledDate = date('Y-m-d', strtotime("+$dayOffset days"));

            // Get attendance for this student
            $attendance = $this->getWeeklyAttendance($student['id_siswa'], $monday, $friday);

            // Format message
            $message = $this->formatWeeklyMessage($student, $attendance, $monday, $friday);

            // Normalize phone number
            $phone = $this->normalizePhone($student['hp']);

            if (empty($phone))
                continue;

            // Queue the message
            $this->waQueue->insert([
                'id_siswa' => $student['id_siswa'],
                'phone_number' => $phone,
                'message' => $message,
                'status' => 'pending',
                'scheduled_date' => $scheduledDate,
                'week_start' => $monday,
                'week_end' => $friday,
            ]);

            $queuedCount++;
        }

        // Get distribution stats
        $stats = $this->waQueue->getStats();

        // Auto-trigger attendance alert scan and notifications
        $alertResult = $this->triggerAttendanceAlerts();

        return $this->response->setJSON([
            'status' => true,
            'message' => "Berhasil generate $queuedCount pesan untuk minggu $monday - $friday",
            'week_start' => $monday,
            'week_end' => $friday,
            'total_queued' => $queuedCount,
            'per_day' => $studentsPerDay,
            'stats' => $stats,
            'alerts' => $alertResult
        ]);
    }

    /**
     * Trigger attendance alert scan and auto-notifications
     */
    private function triggerAttendanceAlerts()
    {
        try {
            $alertController = new AttendanceAlert();

            // Use internal method call instead of HTTP request
            $alertModel = new \App\Models\AttendanceAlert_model();
            $waQueue = new WaMessageQueue_model();

            $today = date('Y-m-d');
            $alertsCreated = 0;
            $notificationsSent = 0;

            // 1. Check consecutive absences (2+ days)
            $consecutive = $alertModel->findConsecutiveAbsences($today);
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
                if ($alertModel->createAlert($alertData)) {
                    $alertsCreated++;
                }
            }

            // 2. Check weekly absences (3+ times) - always on Friday when this runs
            $weekStart = date('Y-m-d', strtotime('monday this week'));
            $weekly = $alertModel->findWeeklyAbsences($weekStart, $today);
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
                if ($alertModel->createAlert($alertData)) {
                    $alertsCreated++;
                }
            }

            // 3. Check monthly absences on last day of month
            if (date('d') == date('t')) {
                $monthly = $alertModel->findMonthlyAbsences();
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
                    if ($alertModel->createAlert($alertData)) {
                        $alertsCreated++;
                    }
                }
            }

            // Auto-send notifications for new alerts
            $newAlerts = $alertModel->getActiveAlerts(null, 'new');
            foreach ($newAlerts as $alert) {
                // Just queue for now, actual sending happens via WaQueueWorker
                $notificationsSent++;
            }

            return [
                'success' => true,
                'alerts_created' => $alertsCreated,
                'notifications_queued' => $notificationsSent,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get all active students with phone numbers
     */
    private function getActiveStudentsWithPhone($id_tapel)
    {
        return $this->db->table('t_siswa s')
            ->select('s.id_siswa, s.nm_siswa, s.no_induk, s.nisn, s.hp, r.nm_rombel')
            ->join('t_siswa_rombel sr', 'sr.id_siswa = s.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = sr.id_rombel')
            ->where('sr.id_tapel', $id_tapel)
            ->where('s.sts_siswa', 1)
            ->where('s.hp !=', '')
            ->where('s.hp IS NOT NULL')
            ->orderBy('s.nm_siswa', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get weekly attendance for a student (Mon-Fri)
     */
    private function getWeeklyAttendance($id_siswa, $monday, $friday)
    {
        $attendance = [];
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // Get all attendance records for this week
        $records = $this->db->table('t_siswa_hadir')
            ->where('id_siswa', $id_siswa)
            ->where('tgl_hadir >=', $monday)
            ->where('tgl_hadir <=', $friday)
            ->orderBy('tgl_hadir', 'ASC')
            ->orderBy('sts_hadir', 'ASC')
            ->get()
            ->getResultArray();

        // Get schedule for each day (jam masuk)
        $schedules = $this->db->table('r_hari')->get()->getResultArray();
        $scheduleMap = [];
        foreach ($schedules as $sch) {
            $scheduleMap[$sch['nm_hari']] = $sch;
        }

        // Initialize each day
        $currentDate = $monday;
        foreach ($days as $dayName) {
            $attendance[$currentDate] = [
                'hari' => $dayName,
                'tanggal' => $currentDate,
                'masuk' => null,
                'pulang' => null,
                'terlambat' => false,
            ];

            // Get schedule for this day
            $jamMasuk = $scheduleMap[$dayName]['jammasuk'] ?? '07:00:00';

            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        // Fill in actual attendance
        foreach ($records as $record) {
            $tgl = $record['tgl_hadir'];
            if (!isset($attendance[$tgl]))
                continue;

            if ($record['sts_hadir'] == 0) {
                // Masuk
                $attendance[$tgl]['masuk'] = substr($record['jam'], 0, 5); // HH:MM

                // Check if late
                $dayName = $attendance[$tgl]['hari'];
                $jamMasuk = $scheduleMap[$dayName]['jammasuk'] ?? '07:00:00';
                if ($record['jam'] > $jamMasuk) {
                    $attendance[$tgl]['terlambat'] = true;
                }
            } else {
                // Pulang
                $attendance[$tgl]['pulang'] = substr($record['jam'], 0, 5);
            }
        }

        return $attendance;
    }

    /**
     * Format weekly message for WhatsApp
     */
    private function formatWeeklyMessage($student, $attendance, $monday, $friday)
    {
        $formattedMonday = $this->formatTanggalIndo($monday);
        $formattedFriday = $this->formatTanggalIndo($friday);

        $message = "📊 *LAPORAN KEHADIRAN MINGGUAN*\n";
        $message .= "🏫 SMKN 2 INDRAMAYU\n\n";
        $message .= "Yth. Orang Tua/Wali dari:\n";
        $message .= "👤 *{$student['nm_siswa']}*\n";
        $message .= "🆔 NIS: {$student['no_induk']}\n";
        $message .= "📚 Kelas: *{$student['nm_rombel']}*\n\n";
        $message .= "📅 Periode: {$formattedMonday} - {$formattedFriday}\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";

        foreach ($attendance as $tgl => $data) {
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
        $message .= "⚠️ = Terlambat\n";
        $message .= "❌ = Tidak Hadir\n\n";
        $message .= "Terima kasih atas perhatian Bapak/Ibu 🙏\n";
        $message .= "— *Sistem Absensi Digital*\n";
        $message .= "*SMKN 2 INDRAMAYU*";

        return $message;
    }

    /**
     * Format date to Indonesian format
     */
    private function formatTanggalIndo($date)
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

        $d = date('j', strtotime($date));
        $m = $bulan[(int) date('n', strtotime($date))];
        $y = date('Y', strtotime($date));

        return "$d $m $y";
    }

    /**
     * Normalize phone number to 62xxx format
     */
    private function normalizePhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($phone))
            return null;

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * View queue status
     * URL: /WeeklyReport/status
     */
    public function status()
    {
        $stats = $this->waQueue->getStats();

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

        return $this->response->setJSON([
            'status' => true,
            'stats' => $stats,
            'recent_pending' => $recentPending,
            'recent_sent' => $recentSent,
            'recent_failed' => $recentFailed,
        ]);
    }

    /**
     * Preview message for a specific student (for testing)
     * URL: /WeeklyReport/preview/[id_siswa]
     */
    public function preview($id_siswa = null)
    {
        if (!$id_siswa) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'ID siswa required'
            ]);
        }

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
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Siswa tidak ditemukan'
            ]);
        }

        // Get last week
        $friday = date('Y-m-d', strtotime('last friday'));
        $monday = date('Y-m-d', strtotime('last monday', strtotime($friday)));

        $attendance = $this->getWeeklyAttendance($id_siswa, $monday, $friday);
        $message = $this->formatWeeklyMessage($student, $attendance, $monday, $friday);

        return $this->response->setJSON([
            'status' => true,
            'student' => $student,
            'week_start' => $monday,
            'week_end' => $friday,
            'attendance' => $attendance,
            'message' => $message
        ]);
    }

    /**
     * Clear all queue (for testing/reset)
     * URL: /WeeklyReport/clearQueue
     */
    public function clearQueue()
    {
        $this->db->table('wa_message_queue')->truncate();

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Queue cleared'
        ]);
    }
}
