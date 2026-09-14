<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AttendanceAlert Model
 * 
 * Handles detection and management of student attendance alerts
 */
class AttendanceAlert_model extends Model
{
    protected $table = 'attendance_alerts';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_siswa',
        'id_rombel',
        'alert_type',
        'alert_date',
        'absence_count',
        'absence_dates',
        'status',
        'notified_walikelas_at',
        'notified_bk_at',
        'resolved_at',
        'resolved_by',
        'notes'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Find students with 2+ consecutive absent days
     * 
     * @param string $checkDate Date to check from (defaults to today)
     * @return array Students with consecutive absences
     */
    public function findConsecutiveAbsences($checkDate = null)
    {
        if (!$checkDate) {
            $checkDate = date('Y-m-d');
        }

        $db = \Config\Database::connect();
        $tapel = session()->get('id_tapel');

        // Get yesterday's date (school days only - simplified, doesn't account for holidays)
        $yesterday = date('Y-m-d', strtotime('-1 day', strtotime($checkDate)));

        // Find students who were absent on both yesterday and the day before
        // Absent = no record in t_siswa_hadir with sts_hadir = 0 (hadir)
        $sql = "
            SELECT 
                s.id_siswa,
                s.nm_siswa,
                s.no_induk,
                sr.id_rombel,
                r.nm_rombel,
                GROUP_CONCAT(DISTINCT missing_dates.check_date ORDER BY missing_dates.check_date) as absence_dates
            FROM t_siswa s
            JOIN t_siswa_rombel sr ON sr.id_siswa = s.id_siswa
            JOIN t_rombel r ON r.id_rombel = sr.id_rombel
            CROSS JOIN (
                SELECT ? as check_date
                UNION ALL SELECT ?
            ) missing_dates
            LEFT JOIN t_siswa_hadir h ON h.id_siswa = s.id_siswa 
                AND h.tgl_hadir = missing_dates.check_date
                AND h.sts_hadir = 0
            WHERE sr.id_tapel = ?
                AND s.sts_siswa = 1
                AND h.id_siswa_hadir IS NULL
            GROUP BY s.id_siswa, s.nm_siswa, s.no_induk, sr.id_rombel, r.nm_rombel
            HAVING COUNT(DISTINCT missing_dates.check_date) >= 2
        ";

        $query = $db->query($sql, [$checkDate, $yesterday, $tapel]);
        return $query->getResultArray();
    }

    /**
     * Find students with 3+ absences in the current week
     * 
     * @param string $weekStart Start of the week (Monday)
     * @param string $weekEnd End of the week (Friday)
     * @return array Students with weekly absence threshold exceeded
     */
    public function findWeeklyAbsences($weekStart = null, $weekEnd = null)
    {
        if (!$weekStart) {
            $weekStart = date('Y-m-d', strtotime('monday this week'));
        }
        if (!$weekEnd) {
            $weekEnd = date('Y-m-d', strtotime('friday this week'));
        }

        $db = \Config\Database::connect();
        $tapel = session()->get('id_tapel');

        // Generate weekdays between start and end
        $weekDays = [];
        $current = strtotime($weekStart);
        $end = strtotime($weekEnd);
        while ($current <= $end) {
            $dayOfWeek = date('N', $current);
            if ($dayOfWeek <= 5) { // Monday-Friday
                $weekDays[] = date('Y-m-d', $current);
            }
            $current = strtotime('+1 day', $current);
        }

        if (empty($weekDays)) {
            return [];
        }

        // Build placeholders for dates
        $datePlaceholders = implode(',', array_fill(0, count($weekDays), '?'));

        $sql = "
            SELECT 
                s.id_siswa,
                s.nm_siswa,
                s.no_induk,
                sr.id_rombel,
                r.nm_rombel,
                COUNT(DISTINCT wd.check_date) as total_weekdays,
                COUNT(DISTINCT h.tgl_hadir) as present_days,
                (COUNT(DISTINCT wd.check_date) - COUNT(DISTINCT h.tgl_hadir)) as absent_count,
                GROUP_CONCAT(
                    DISTINCT CASE WHEN h.tgl_hadir IS NULL THEN wd.check_date END 
                    ORDER BY wd.check_date
                ) as absence_dates
            FROM t_siswa s
            JOIN t_siswa_rombel sr ON sr.id_siswa = s.id_siswa
            JOIN t_rombel r ON r.id_rombel = sr.id_rombel
            CROSS JOIN (
                SELECT ? as check_date
                " . str_repeat(" UNION ALL SELECT ? ", count($weekDays) - 1) . "
            ) wd
            LEFT JOIN t_siswa_hadir h ON h.id_siswa = s.id_siswa 
                AND h.tgl_hadir = wd.check_date
                AND h.sts_hadir = 0
            WHERE sr.id_tapel = ?
                AND s.sts_siswa = 1
            GROUP BY s.id_siswa, s.nm_siswa, s.no_induk, sr.id_rombel, r.nm_rombel
            HAVING absent_count >= 3
        ";

        $params = array_merge($weekDays, [$tapel]);
        $query = $db->query($sql, $params);
        return $query->getResultArray();
    }

    /**
     * Find students with 10+ absences in the month
     * 
     * @param int $month Month number (1-12)
     * @param int $year Year
     * @return array Students with monthly absence threshold exceeded
     */
    public function findMonthlyAbsences($month = null, $year = null)
    {
        if (!$month) {
            $month = date('n');
        }
        if (!$year) {
            $year = date('Y');
        }

        $db = \Config\Database::connect();
        $tapel = session()->get('id_tapel');

        // Get first and last day of month
        $monthStart = sprintf('%04d-%02d-01', $year, $month);
        $monthEnd = date('Y-m-t', strtotime($monthStart));

        // Generate all weekdays in the month
        $weekDays = [];
        $current = strtotime($monthStart);
        $end = strtotime($monthEnd);
        while ($current <= $end) {
            $dayOfWeek = date('N', $current);
            if ($dayOfWeek <= 5) { // Monday-Friday
                $weekDays[] = date('Y-m-d', $current);
            }
            $current = strtotime('+1 day', $current);
        }

        if (empty($weekDays)) {
            return [];
        }

        // Simplified approach: count records vs expected days
        $sql = "
            SELECT 
                s.id_siswa,
                s.nm_siswa,
                s.no_induk,
                sr.id_rombel,
                r.nm_rombel,
                ? as total_school_days,
                COUNT(DISTINCT h.tgl_hadir) as present_days,
                (? - COUNT(DISTINCT h.tgl_hadir)) as absent_count
            FROM t_siswa s
            JOIN t_siswa_rombel sr ON sr.id_siswa = s.id_siswa
            JOIN t_rombel r ON r.id_rombel = sr.id_rombel
            LEFT JOIN t_siswa_hadir h ON h.id_siswa = s.id_siswa 
                AND MONTH(h.tgl_hadir) = ?
                AND YEAR(h.tgl_hadir) = ?
                AND h.sts_hadir = 0
            WHERE sr.id_tapel = ?
                AND s.sts_siswa = 1
            GROUP BY s.id_siswa, s.nm_siswa, s.no_induk, sr.id_rombel, r.nm_rombel
            HAVING absent_count >= 10
        ";

        $totalDays = count($weekDays);
        $query = $db->query($sql, [$totalDays, $totalDays, $month, $year, $tapel]);
        return $query->getResultArray();
    }

    /**
     * Create or update an alert
     */
    public function createAlert($data)
    {
        // Check if alert already exists for this student/type/date
        $existing = $this->where('id_siswa', $data['id_siswa'])
            ->where('alert_type', $data['alert_type'])
            ->where('alert_date', $data['alert_date'])
            ->first();

        if ($existing) {
            // Update existing alert
            return $this->update($existing['id'], [
                'absence_count' => $data['absence_count'],
                'absence_dates' => $data['absence_dates'],
            ]);
        }

        // Create new alert
        return $this->insert($data);
    }

    /**
     * Get all active (unresolved) alerts with student and class info
     */
    public function getActiveAlerts($alertType = null, $status = null)
    {
        $builder = $this->db->table($this->table)
            ->select('attendance_alerts.*, s.nm_siswa, s.no_induk, r.nm_rombel, 
                      ptk.nama_ptk as wali_kelas_nama, ptk.no_hp as wali_kelas_hp,
                      bk.nama_ptk as guru_bk_nama, bk.no_hp as guru_bk_hp')
            ->join('t_siswa s', 's.id_siswa = attendance_alerts.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = attendance_alerts.id_rombel')
            ->join('t_ptk ptk', 'ptk.id_ptk = r.id_walikelas', 'left')
            ->join('t_ptk bk', 'bk.id_ptk = r.id_guru_bk', 'left')
            ->where('attendance_alerts.status !=', 'resolved');

        if ($alertType) {
            $builder->where('attendance_alerts.alert_type', $alertType);
        }

        if ($status) {
            $builder->where('attendance_alerts.status', $status);
        }

        return $builder->orderBy('attendance_alerts.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get alert statistics
     */
    public function getAlertStats()
    {
        $db = \Config\Database::connect();

        $stats = [
            'total_new' => 0,
            'total_notified_walikelas' => 0,
            'total_notified_bk' => 0,
            'total_resolved' => 0,
            'by_type' => [
                'consecutive' => 0,
                'weekly' => 0,
                'monthly' => 0,
            ],
        ];

        // Count by status
        $statusCounts = $db->table($this->table)
            ->select('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        foreach ($statusCounts as $row) {
            $key = 'total_' . $row['status'];
            if (isset($stats[$key])) {
                $stats[$key] = (int) $row['count'];
            }
        }

        // Count by type (active only)
        $typeCounts = $db->table($this->table)
            ->select('alert_type, COUNT(*) as count')
            ->where('status !=', 'resolved')
            ->groupBy('alert_type')
            ->get()
            ->getResultArray();

        foreach ($typeCounts as $row) {
            if (isset($stats['by_type'][$row['alert_type']])) {
                $stats['by_type'][$row['alert_type']] = (int) $row['count'];
            }
        }

        return $stats;
    }

    /**
     * Get alert history (resolved alerts) for reports
     */
    public function getAlertHistory($filters = [])
    {
        $builder = $this->db->table($this->table)
            ->select('attendance_alerts.*, s.nm_siswa, s.no_induk, r.nm_rombel')
            ->join('t_siswa s', 's.id_siswa = attendance_alerts.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = attendance_alerts.id_rombel');

        if (!empty($filters['start_date'])) {
            $builder->where('attendance_alerts.alert_date >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('attendance_alerts.alert_date <=', $filters['end_date']);
        }

        if (!empty($filters['alert_type'])) {
            $builder->where('attendance_alerts.alert_type', $filters['alert_type']);
        }

        if (!empty($filters['id_rombel'])) {
            $builder->where('attendance_alerts.id_rombel', $filters['id_rombel']);
        }

        return $builder->orderBy('attendance_alerts.alert_date', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Mark alert as notified to wali kelas
     */
    public function markNotifiedWalikelas($id)
    {
        return $this->update($id, [
            'status' => 'notified_walikelas',
            'notified_walikelas_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Mark alert as notified to guru BK
     */
    public function markNotifiedBK($id)
    {
        return $this->update($id, [
            'status' => 'notified_bk',
            'notified_bk_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Mark alert as resolved
     */
    public function markResolved($id, $userId = null, $notes = null)
    {
        return $this->update($id, [
            'status' => 'resolved',
            'resolved_at' => date('Y-m-d H:i:s'),
            'resolved_by' => $userId,
            'notes' => $notes,
        ]);
    }
}
