<?php

namespace App\Models;

use CodeIgniter\Model;

class Libur_model extends Model
{
    protected $table = 'libur';

    public function getLibur()
    {
        return $this->db->table($this->table)
        ->join('t_ptk','t_ptk.id_ptk = libur.id_ptk')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->orderBy('tgl_entri', 'Desc')
        ->get()->getResultArray();
    }
   
    public function saveLibur($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }

    public function editLibur($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_libur', $id);
        return $builder->update($data);
    }
    public function hapusLibur($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_libur' => $id]);
    }
}
