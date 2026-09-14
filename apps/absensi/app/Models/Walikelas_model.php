<?php

namespace App\Models;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class Walikelas_model extends Model
{
    protected $table = 't_walikelas';

    public function getWalikelas()
    {
        return $this->db->table($this->table)
        ->orderBy('id_walikelas')
        ->get()->getResultArray();
    }
    public function getWalikelassnonrombel($tapel)
    {
        $db = \Config\Database::connect();
        $subQuery = $db->table('t_setting_walikelas')->select('id_walikelas')->where('id_tapel', $tapel);
        return $this->db->table($this->table)
        ->whereNotIn('id_walikelas', $subQuery)
        ->get()->getResultArray();
    }
   
    public function saveWalikelas($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }

    public function editWalikelas($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_walikelas', $id);
        return $builder->update($data);
    }
   
    public function hapusWalikelas($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_walikelas' => $id]);
    }
}
