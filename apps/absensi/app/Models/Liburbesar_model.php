<?php

namespace App\Models;

use CodeIgniter\Model;

class Liburbesar_model extends Model
{
    protected $table = 'libur_besar';

    public function getLiburbesar()
    {
        return $this->db->table($this->table)
        ->get()->getResultArray();
    }
   
    public function saveLiburbesar($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
   
    public function editLiburbesar($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_liburbesar', $id);
        return $builder->update($data);
    }
    public function hapusLiburbesar($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_liburbesar' => $id]);
    }

}
