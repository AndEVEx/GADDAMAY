<?php

namespace App\Models;

use CodeIgniter\Model;

class Katgaji_model extends Model
{
    protected $table = 'r_kat_gaji';

    public function getKatgaji()
    {
        return $this->db->table($this->table)
        ->get()->getResultArray();
    }
    public function saveKatgaji($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insertBatch($data);
    }
   
    public function editKatgaji($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_kat_gaji', $id);
        return $builder->update($data);
    }
    public function hapusKatgaji($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_kat_gaji' => $id]);
    }

}
