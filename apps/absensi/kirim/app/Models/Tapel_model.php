<?php

namespace App\Models;

use CodeIgniter\Model;

class Tapel_model extends Model
{
    protected $table = 'r_tapel';

    public function getTapel()
    {
        return $this->db->table($this->table)
        ->get()->getResultArray();
    }
   
    public function saveTapel($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
   
    public function editTapel($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_tapel', $id);
        return $builder->update($data);
    }
    public function hapusTapel($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_tapel' => $id]);
    }

}
