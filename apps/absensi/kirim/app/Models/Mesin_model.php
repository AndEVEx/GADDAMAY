<?php

namespace App\Models;

use CodeIgniter\Model;

class Mesin_model extends Model
{
    protected $table = 'tweb_mesin';

    public function getMesin()
    {
        return $this->db->table($this->table)
        ->get()->getResultArray();
    }

    public function saveMesin($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
    public function saveCommand($data)
    {
        $builder = $this->db->table('tweb_command_adms');
        return $builder->insert($data);
    }

    public function editMesin($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('ID', $id);
        return $builder->update($data);
    }
    public function hapusMesin($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['ID' => $id]);
    }
}
