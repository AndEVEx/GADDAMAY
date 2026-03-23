<?php

namespace App\Models;

use CodeIgniter\Model;

class MuridMonitoring_model extends Model
{
    protected $table = 't_murid_monitoring';
    protected $primaryKey = 'id_monitoring';

    /**
     * Get all active monitoring cases for a rombel
     */
    public function getMonitoringByRombel($id_rombel, $id_tapel)
    {
        return $this->db->table('t_murid_monitoring m')
            ->select('m.*, s.no_induk, s.nm_siswa, s.hp as hp_siswa, r.nm_rombel, 
                      wali.nama_ptk as nm_walikelas, bk.nama_ptk as nm_guru_bk')
            ->join('t_siswa s', 's.id_siswa = m.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = m.id_rombel')
            ->join('t_ptk wali', 'wali.id_ptk = r.id_walikelas', 'LEFT')
            ->join('t_ptk bk', 'bk.id_ptk = r.id_guru_bk', 'LEFT')
            ->where('m.id_rombel', $id_rombel)
            ->where('m.id_tapel', $id_tapel)
            ->where('m.status', 'active')
            ->orderBy('m.created_at', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Get all active monitoring cases for multiple rombels
     */
    public function getMonitoringByRombels($id_rombels, $id_tapel)
    {
        return $this->db->table('t_murid_monitoring m')
            ->select('m.*, s.no_induk, s.nm_siswa, s.hp as hp_siswa, r.nm_rombel,
                      wali.nama_ptk as nm_walikelas, bk.nama_ptk as nm_guru_bk')
            ->join('t_siswa s', 's.id_siswa = m.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = m.id_rombel')
            ->join('t_ptk wali', 'wali.id_ptk = r.id_walikelas', 'LEFT')
            ->join('t_ptk bk', 'bk.id_ptk = r.id_guru_bk', 'LEFT')
            ->whereIn('m.id_rombel', $id_rombels)
            ->where('m.id_tapel', $id_tapel)
            ->where('m.status', 'active')
            ->orderBy('m.created_at', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Get ALL active monitoring cases (for kepsek view)
     */
    public function getMonitoringAll($id_tapel)
    {
        return $this->db->table('t_murid_monitoring m')
            ->select('m.*, s.no_induk, s.nm_siswa, s.hp as hp_siswa, r.nm_rombel,
                      wali.nama_ptk as nm_walikelas, bk.nama_ptk as nm_guru_bk')
            ->join('t_siswa s', 's.id_siswa = m.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = m.id_rombel')
            ->join('t_ptk wali', 'wali.id_ptk = r.id_walikelas', 'LEFT')
            ->join('t_ptk bk', 'bk.id_ptk = r.id_guru_bk', 'LEFT')
            ->where('m.id_tapel', $id_tapel)
            ->where('m.status', 'active')
            ->orderBy('m.created_at', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Get single monitoring case by ID
     */
    public function getMonitoringById($id)
    {
        return $this->db->table('t_murid_monitoring m')
            ->select('m.*, s.no_induk, s.nm_siswa, s.hp as hp_siswa, s.file as foto_siswa,
                      r.nm_rombel, wali.nama_ptk as nm_walikelas, bk.nama_ptk as nm_guru_bk')
            ->join('t_siswa s', 's.id_siswa = m.id_siswa')
            ->join('t_rombel r', 'r.id_rombel = m.id_rombel')
            ->join('t_ptk wali', 'wali.id_ptk = r.id_walikelas', 'LEFT')
            ->join('t_ptk bk', 'bk.id_ptk = r.id_guru_bk', 'LEFT')
            ->where('m.id_monitoring', $id)
            ->get()->getRowArray();
    }

    /**
     * Get all 4 progress steps for a monitoring case
     */
    public function getProgress($id_monitoring)
    {
        return $this->db->table('t_monitoring_progress')
            ->where('id_monitoring', $id_monitoring)
            ->orderBy('step', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Add new monitoring case + auto-create 4 progress rows
     */
    public function addMonitoring($data)
    {
        $this->db->table('t_murid_monitoring')->insert($data);
        $id_monitoring = $this->db->insertID();

        // Create 4 progress steps
        for ($i = 1; $i <= 4; $i++) {
            $this->db->table('t_monitoring_progress')->insert([
                'id_monitoring' => $id_monitoring,
                'step' => $i,
                'is_done' => 0
            ]);
        }

        return $id_monitoring;
    }

    /**
     * Mark a step as done with proof file
     */
    public function completeStep($id_monitoring, $step, $file, $file_type, $done_by, $catatan = null)
    {
        $this->db->table('t_monitoring_progress')
            ->where('id_monitoring', $id_monitoring)
            ->where('step', $step)
            ->update([
                'is_done' => 1,
                'file_bukti' => $file,
                'file_type' => $file_type,
                'catatan' => $catatan,
                'done_by' => $done_by,
                'done_at' => date('Y-m-d H:i:s')
            ]);

        // Update current_step on monitoring
        $this->db->table('t_murid_monitoring')
            ->where('id_monitoring', $id_monitoring)
            ->update(['current_step' => $step]);

        return true;
    }

    /**
     * Mark case as resolved
     */
    public function resolveCase($id_monitoring)
    {
        return $this->db->table('t_murid_monitoring')
            ->where('id_monitoring', $id_monitoring)
            ->update([
                'status' => 'resolved',
                'resolved_at' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * Reopen: create new monitoring case at next step level
     */
    public function reopenCase($id_siswa, $id_rombel, $id_tapel, $alasan, $created_by, $start_step)
    {
        $data = [
            'id_siswa' => $id_siswa,
            'id_rombel' => $id_rombel,
            'id_tapel' => $id_tapel,
            'alasan' => $alasan,
            'current_step' => $start_step,
            'status' => 'active',
            'created_by' => $created_by
        ];

        $this->db->table('t_murid_monitoring')->insert($data);
        $id_monitoring = $this->db->insertID();

        // Create 4 progress steps, mark steps before start_step as done
        for ($i = 1; $i <= 4; $i++) {
            $this->db->table('t_monitoring_progress')->insert([
                'id_monitoring' => $id_monitoring,
                'step' => $i,
                'is_done' => ($i < $start_step) ? 1 : 0,
                'catatan' => ($i < $start_step) ? 'Auto-completed from previous case' : null,
                'done_at' => ($i < $start_step) ? date('Y-m-d H:i:s') : null
            ]);
        }

        return $id_monitoring;
    }

    /**
     * Get rombel IDs where this guru is walikelas
     */
    public function getRombelWalikelas($id_ptk, $id_tapel)
    {
        return $this->db->table('t_rombel')
            ->select('id_rombel, nm_rombel')
            ->where('id_walikelas', $id_ptk)
            ->where('id_tapel', $id_tapel)
            ->get()->getResultArray();
    }

    /**
     * Get rombel IDs where this guru is BK
     */
    public function getRombelBK($id_ptk, $id_tapel)
    {
        return $this->db->table('t_rombel')
            ->select('id_rombel, nm_rombel')
            ->where('id_guru_bk', $id_ptk)
            ->where('id_tapel', $id_tapel)
            ->get()->getResultArray();
    }
}
