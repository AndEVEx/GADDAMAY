<?php

namespace App\Models;

use CodeIgniter\Model;

class Tingkatkelas_model extends Model
{
    protected $table = 'r_tingkat_kelas';

    public function getTingkat()
    {
        return $this->db->table($this->table)
        ->get()->getResultArray();
    }
   
    public function saveTingkat($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
   
    public function editTingkat($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_tingkat_kelas', $id);
        return $builder->update($data);
    }
    public function hapusTingkat($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_tingkat_kelas' => $id]);
    }

}
