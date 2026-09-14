<?php

namespace App\Models;

use CodeIgniter\Model;

class Setting_model extends Model
{
    protected $table = 't_setting_aplikasi';

    public function getSetting()
    {
        return $this->db->table($this->table)
        ->get()->getResultArray();
    }

    public function saveSetting($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }

    public function editSetting($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_setting', $id);
        return $builder->update($data);
    }
    
}
