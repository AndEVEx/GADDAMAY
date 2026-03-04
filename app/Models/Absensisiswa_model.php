<?php

namespace App\Models;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class Absensisiswa_model extends Model
{
    protected $table = 't_siswa_hadir';

    public function getAbsensi()
    {
        return $this->db->table($this->table)
        ->join('t_siswa', 't_siswa.id_siswa = t_siswa_hadir.id_siswa')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa_hadir.id_siswa')
        ->where('sts_hadir', 0)
        ->orderBy('nm_siswa', 'ASC')
        ->get()->getResultArray();
    } 
    public function getAbsensihari($tgl)
    {
        return $this->db->table($this->table)
        ->join('t_siswa', 't_siswa.id_siswa = t_siswa_hadir.id_siswa')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa_hadir.id_siswa')
        ->where('sts_hadir', 0)
        ->where('tgl_hadir', $tgl)
        ->orderBy('nm_siswa', 'ASC')
        ->get()->getResultArray();
    }
    public function getAbsensisiswa($id, $tgl)
    {
        return $this->db->table($this->table)
        ->select('t_siswa_hadir.id_siswa as id_siswa,no_induk,nm_siswa,nm_rombel,jam,sts_hadir,file')
        ->join('t_siswa', 't_siswa.id_siswa = t_siswa_hadir.id_siswa')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa_hadir.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('tgl_hadir', $tgl)
        ->where('t_siswa_hadir.id_siswa', $id)
        ->get()->getResultArray();
    }
    public function getAbsensisiswatgl($id, $tgl1, $tgl2)
    {
        return $this->db->table($this->table)
        ->where('tgl_hadir >=', $tgl1)
        ->where('tgl_hadir <=', $tgl2)
        ->where('t_siswa_hadir.id_siswa', $id)
        ->groupBy('tgl_hadir')
        ->get()->getResultArray();
    }
    
    public function getAbsensisiswabln($id, $bln)
    {
        return $this->db->table($this->table)
        ->where('MONTH(tgl_hadir)', $bln)
        ->where('t_siswa_hadir.id_siswa', $id)
        ->get()->getResultArray();
    }
    public function getAbsensihadir($tgl)
    {
        return $this->db->table($this->table)
        ->select('t_siswa_hadir.id_siswa as id_siswa,no_induk,nm_siswa,nm_rombel')
        ->join('t_siswa', 't_siswa.id_siswa = t_siswa_hadir.id_siswa')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa_hadir.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('sts_hadir', 0)
        ->where('tgl_hadir', $tgl)
        ->where('t_siswa_rombel.id_tapel', session()->get('id_tapel'))
        ->orderBy('nm_siswa', 'ASC') 
        ->get()->getResultArray();
    } 
    public function getAbsensipulang($tgl)
    {
        return $this->db->table($this->table)
        ->select('t_siswa_hadir.id_siswa as id_siswa,no_induk,nm_siswa,nm_rombel')
        ->join('t_siswa', 't_siswa.id_siswa = t_siswa_hadir.id_siswa')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa_hadir.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('sts_hadir', 1)
        ->where('tgl_hadir', $tgl)
        ->orderBy('nm_siswa', 'ASC')
        ->get()->getResultArray();
    }
    public function getAbsensiterlambat($tgl,$jam)
    {
        return $this->db->table($this->table)
        ->select('t_siswa_hadir.id_siswa as id_siswa,no_induk,nm_siswa,nm_rombel')
        ->join('t_siswa', 't_siswa.id_siswa = t_siswa_hadir.id_siswa')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa_hadir.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('sts_hadir', 0)
        ->where('tgl_hadir', $tgl)
        ->where('jam >', $jam)
        ->orderBy('nm_siswa', 'ASC')
        ->get()->getResultArray();
    }
    
    public function saveAbsensi($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
   
    public function editAbsensi($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_siswa_hadir', $id);
        return $builder->update($data);
    }
    public function editAbsensipulang($data, $id, $tgl)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_siswa', $id);
        $builder->where('tgl_hadir', $tgl);
        $builder->where('sts_hadir', 1);
        return $builder->update($data);
    }
  
    public function hapusAbsensi($id, $tgl)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_siswa_hadir', $id);
        return $builder->delete();
    }

}
