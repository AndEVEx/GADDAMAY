<?php

namespace App\Models;

use CodeIgniter\Model;

class Totalpoint_model extends Model
{
    protected $table = 't_total_point';

    public function getTotalpoint($bln, $thn)
    {
        return $this->db->table($this->table)
        ->select('nama_ptk, jml_point')
        ->join('t_ptk', 't_ptk.id_ptk = t_total_point.id_ptk')
        ->where('bln', $bln)
        ->where('thn', $thn)
        ->where('id_jenis_ptk', 2)
        ->orderBy('jml_point', 'desc')
        ->limit(10)
        ->get()->getResultArray();
    } 
    public function getTotalpointterbaik($bln, $thn)
    {
        return $this->db->table($this->table)
        ->select('nama_ptk, jml_point, nama_jenis_ptk')
        ->join('t_ptk', 't_ptk.id_ptk = t_total_point.id_ptk')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->where('bln', $bln)
        ->where('thn', $thn)
        ->orderBy('jml_point', 'desc')
        ->limit(10)
        ->get()->getResultArray();
    } 
   
    public function saveTotalpoint($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insertBatch($data);
    }

    public function editTotalpoint($data, $bln, $thn, $idptk)
    {
        $builder = $this->db->table($this->table);
        $builder->where('bln', $bln);
        $builder->where('thn', $thn);
        $builder->where('id_ptk', $idptk);
        return $builder->update($data);
    }
    public function hapusTotalpoint($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_total_point' => $id]);
    }
}
