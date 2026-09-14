<?php

namespace App\Models;

use CodeIgniter\Model;

class Gaji_model extends Model
{
    protected $table = 'gaji';

    public function getGaji()
    {
        return $this->db->table($this->table)
        ->get()->getResultArray();
    }
    public function saveGaji($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insertBatch($data);
    }
    public function saveGajiapprove($data)
    {
        $builder = $this->db->table('gaji_approve');
        return $builder->insertBatch($data);
    }
    public function editGaji($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->updateBatch($data, ['id_ptk', 'id_kat_gaji','bln','thn']);
    }
    public function hapusGaji($id,$bln,$thn)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_ptk' => $id,'bln' => $bln,'thn' => $thn]);
    }

} 
