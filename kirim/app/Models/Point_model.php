<?php

namespace App\Models;

use CodeIgniter\Model;

class Point_model extends Model
{
    protected $table = 't_point';

    public function getPointdosen()
    {
        return $this->db->table($this->table)
        ->select('nama_ptk, sum(nilai) as point')
        ->join('t_ptk', 't_ptk.id_ptk = t_point.id_ptk')
        ->where('id_jenis_ptk', 1)
        ->where('status_ptk', 1)
        ->orderBy('point', 'desc')
        ->limit(5)
        ->get()->getResultArray();
    }
    public function getPointkaryawan()
    {
        return $this->db->table($this->table)
        ->select('nama_ptk, sum(nilai) as point')
        ->join('t_ptk', 't_ptk.id_ptk = t_point.id_ptk')
        ->where('id_jenis_ptk', 2)
        ->where('status_ptk', 1)
        ->orderBy('point', 'desc')
        ->limit(5)
        ->get()->getResultArray();
    }
    public function savePoint($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insertBatch($data);
    }

    public function editPoint($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_point', $id);
        return $builder->update($data);
    }
    public function hapusPoint($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_point' => $id]);
    }
}
