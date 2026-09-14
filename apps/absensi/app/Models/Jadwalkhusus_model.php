<?php

namespace App\Models;

use CodeIgniter\Model;

class Jadwalkhusus_model extends Model
{
    protected $table = 'jadwal_khusus';

    public function getJadwalkhusus()
    {
        return $this->db->table($this->table)
        ->join('t_ptk','t_ptk.id_ptk = jadwal_khusus.id_ptk')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->orderBy('tgl_entri', 'Desc')
        ->get()->getResultArray();
    }
   
    public function saveJadwalkhusus($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }

    public function editJadwalkhusus($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_jadwal', $id);
        return $builder->update($data);
    }
    public function hapusJadwalkhusus($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_jadwal' => $id]);
    }
}
