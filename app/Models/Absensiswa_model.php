<?php

namespace App\Models;

use CodeIgniter\Model;

class Absensiswa_model extends Model
{
    protected $table = 't_siswa_absen';

    public function getAbsen()
    {
        return $this->db->table($this->table)
      
        ->get()->getResultArray();
    }
    public function getAbsensiswa($id)
    {
        return $this->db->table($this->table)
        ->where('id_siswa',$id)
        ->orderBy('id_siswa_absen','Desc')
        ->get()->getResultArray();
    }
    public function getAbsenblmapprove()
    {
        return $this->db->table($this->table)
        ->select('id_siswa_absen,t_siswa_absen.id_siswa as id_siswa,nm_siswa,tgl_entri,sts_absen,ket_absen,sts_approve,tgl_absen,t_siswa_absen.file as file')
        ->join('t_siswa','t_siswa.id_siswa = t_siswa_absen.id_siswa')
        ->where('sts_approve', 0)
        ->get()->getResultArray();
    }
    public function getAbsenijin()
    {
        return $this->db->table($this->table)
        ->select('id_siswa_absen,t_siswa_absen.id_siswa as id_siswa,nm_siswa,tgl_entri,sts_absen,ket_absen,sts_approve,tgl_absen,t_siswa_absen.file as file')
        ->join('t_siswa','t_siswa.id_siswa = t_siswa_absen.id_siswa')
        ->where('sts_approve', 1)
        ->get()->getResultArray();
    }
    public function saveAbsen($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
   
    public function editAbsen($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_siswa_absen', $id);
        return $builder->update($data);
    }
    public function editAbsensiswa($data, $id, $tgl)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_siswa', $id);
        $builder->where('tgl_absen', $tgl);
        return $builder->update($data);
    }
    public function hapusAbsenmengajar($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_siswa_absen' => $id]);
    }

}
