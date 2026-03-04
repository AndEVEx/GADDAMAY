<?php

namespace App\Models;

use CodeIgniter\Model;

class Hari_model extends Model
{
    protected $table = 'r_hari';

    public function getHari()
    {
        return $this->db->table($this->table)
        ->get()->getResultArray();
    }
   
    public function saveHari($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
   
    public function editHari($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_hari', $id);
        return $builder->update($data);
    }
    public function hapusHari($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_hari' => $id]);
    }

}
