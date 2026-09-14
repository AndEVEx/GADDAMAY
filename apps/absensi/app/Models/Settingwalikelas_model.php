<?php

namespace App\Models;

use CodeIgniter\Model;

class Settingwalikelas_model extends Model
{
    protected $table = 't_setting_walikelas';

    public function getSettingwalikelas()
    {
        return $this->db->table($this->table)
        ->get()->getResultArray();
    }
    

    public function saveSettingwalikelas($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insertBatch($data);
    }
   
    public function editSettingwalikelas($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_settingwalikelas', $id);
        return $builder->update($data);
    }
    public function hapusSettingwalikelas($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_setting_walikelas' => $id]);
    }
    public function hapusSettingwalikelas1()
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_rombel' => ""]);
    }
}
