<?php

namespace App\Models;

use CodeIgniter\Model;

class Absenguru_model extends Model
{
    protected $table = 'tweb_pegawai_absen';

    public function getAbsen()
    {
        return $this->db->table($this->table)
       
        ->get()->getResultArray();
    }
    public function getAbsenpegawai($id)
    {
        return $this->db->table($this->table)
        
        ->where('ID_PEGAWAI', $id)
        ->get()->getResultArray();
    }
    public function getAbsenblmapprove()
    {
        return $this->db->table($this->table)
        ->select('ID_PEGAWAI,nama_ptk,TANGGAL_AKSES,STATUS,KETERANGAN,STS,TANGGAL_ABSEN,FILE')
        ->join('t_ptk','t_ptk.id_ptk = tweb_pegawai_absen.ID_PEGAWAI')
        ->where('STS', 0)
        ->get()->getResultArray();
    }
    public function getAbsenijin()
    {
        return $this->db->table($this->table)
        ->select('ID_PEGAWAI,nama_ptk,TANGGAL_AKSES,STATUS,KETERANGAN,STS,TANGGAL_ABSEN,FILE')
        ->join('t_ptk','t_ptk.id_ptk = tweb_pegawai_absen.ID_PEGAWAI')
        ->where('STS', 1)
        ->get()->getResultArray();
    }
   
    public function saveAbsen($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
   
    public function editAbsen($data, $id, $tgl)
    {
        $builder = $this->db->table($this->table);
        $builder->where('ID_PEGAWAI', $id);
        $builder->where('TANGGAL_ABSEN', $tgl);
        return $builder->update($data);
    }

    public function hapusAbsen($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['ID_PEGAWAI' => $id]);
    }

}
