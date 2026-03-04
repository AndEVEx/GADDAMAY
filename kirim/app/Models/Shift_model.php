<?php

namespace App\Models;

use CodeIgniter\Model;

class Shift_model extends Model
{
    protected $table = 'r_shift';

    public function getShift()
    {
        return $this->db->table($this->table)
        ->get()->getResultArray();
    }
   
    public function saveShift($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
   
    public function editShift($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_shift', $id);
        return $builder->update($data);
    }
    public function hapusShift($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_shift' => $id]);
    }

}
