<?php

namespace App\Models;

use CodeIgniter\Model;

class Lembur_model extends Model
{
    protected $table = 't_lembur';

    public function getLembur()
    {
        return $this->db->table($this->table)
        ->get()->getResultArray();
    }

    public function saveLembur($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insertBatch($data);
    }

    public function editLembur($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_lembur', $id);
        return $builder->update($data);
    }
    public function hapusLembur($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_lembur' => $id]);
    }
}
