<?php

namespace App\Models;

use CodeIgniter\Model;

class Pointsiswa_model extends Model
{
    protected $table = 't_point_siswa';

    public function getPointsiswabln($id,$bln)
    {
        return $this->db->table($this->table)
        ->where('MONTH(tgl_point)', $bln)
        ->where('YEAR(tgl_point)', date('Y'))
        ->where('id_siswa', $id)
        ->get()->getResultArray();
    }
    public function getPointsisterbaik($bln)
    {
        return $this->db->table($this->table)
        ->select('nm_siswa, sum(nilai) as point, nm_rombel')
        ->join('t_siswa','t_siswa.id_siswa = t_point_siswa.id_siswa')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('MONTH(tgl_point)', $bln)
        ->where('YEAR(tgl_point)', date('Y'))
        ->where('sts_siswa', 1)
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
