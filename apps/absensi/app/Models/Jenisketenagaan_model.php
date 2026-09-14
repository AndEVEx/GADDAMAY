<?php

namespace App\Models;

use CodeIgniter\Model;

class Jenisketenagaan_model extends Model
{
    protected $table = 'r_jenis_ptk';

    public function getJenis()
    {
        return $this->db->table($this->table)
        ->get()->getResultArray();
    }
   
    public function saveJenis($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
   
    public function editJenis($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_jenis_ptk', $id);
        return $builder->update($data);
    }
    public function hapusJenis($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_jenis_ptk' => $id]);
    }

}
