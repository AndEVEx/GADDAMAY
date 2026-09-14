<?php

namespace App\Models;

use CodeIgniter\Model;

class Siswarombel_model extends Model
{
    protected $table = 't_siswa_rombel';

    public function getSiswarombel($id)
    {
        return $this->db->table($this->table)
        ->select('t_siswa_rombel.id_siswa as id_siswa, no_induk, nm_siswa, nm_rombel, jk, id_siswa_rombel, t_siswa_rombel.id_rombel as id_rombel')
        ->join('t_siswa','t_siswa.id_siswa = t_siswa_rombel.id_siswa', 'LEFT')
        ->join('t_rombel','t_rombel.id_rombel = t_siswa_rombel.id_rombel', 'LEFT')
        ->where('t_siswa_rombel.id_rombel', $id)
        ->get()->getResultArray();
    }
    
    public function saveSiswarombelimport($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
    public function saveSiswarombel($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insertBatch($data);
    }
   
    public function editSiswarombel($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_siswa_rombel', $id);
        return $builder->update($data);
    }
    public function editSiswarombelimport($data, $id, $tapel)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_siswa', $id);
        $builder->where('id_tapel', $tapel);
        return $builder->update($data);
    }
    public function hapusSiswarombel($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_rombel' => $id]);
    }
    public function hapusSiswarombel1()
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_rombel' => ""]);
    }
    public function hapusSiswarombelidsiswa($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_siswa' => $id]);
    }
}
