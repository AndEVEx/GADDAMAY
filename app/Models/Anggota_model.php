<?php

namespace App\Models;

use CodeIgniter\Model;

class Anggota_model extends Model
{
    protected $table = 't_anggota_shift';

    public function getAnggota($id)
    {
        return $this->db->table($this->table)
        ->select('id_anggota_shift,nik,nomor_absensi,nama_ptk,nama_jenis_ptk,id_shift')
        ->where('id_shift', $id)
        ->join('t_ptk', 't_ptk.id_ptk = t_anggota_shift.id_ptk')
        ->join('r_jenis_ptk','r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->get()->getResultArray();
    }
   
    public function saveAnggota($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insertBatch($data);
    }
   
    public function editAnggota($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_anggota_shift', $id);
        return $builder->update($data);
    }
    public function hapusAnggota($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_anggota_shift' => $id]);
    }
    public function hapusAnggotaall($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_shift' => $id]);
    }
    public function hapusAnggota1()
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_shift' => ""]);
    }

}
