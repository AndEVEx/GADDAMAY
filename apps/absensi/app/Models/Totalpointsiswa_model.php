<?php

namespace App\Models;

use CodeIgniter\Model;

class Totalpointsiswa_model extends Model
{
    protected $table = 't_total_point_siswa';

    public function getTotalpointsiswa($bln,  $id_tapel)
    {
        return $this->db->table($this->table)
        ->select('no_induk, nm_siswa, jml_point, nm_rombel')
        ->join('t_siswa', 't_siswa.id_siswa = t_total_point.id_siswa')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('bln', $bln)
        ->where('t_total_point.id_tapel',  $id_tapel)
        ->orderBy('jml_point', 'desc')
        ->limit(50)
        ->get()->getResultArray();
    } 
    public function getTotalpointsiswaterbaik($bln,  $id_tapel)
    {
        return $this->db->table($this->table)
        ->select('no_induk, nm_siswa, jml_point, nm_rombel')
        ->join('t_siswa', 't_siswa.id_siswa = t_total_point_siswa.id_siswa')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('bln', $bln)
        ->where('t_total_point_siswa.id_tapel',  $id_tapel)
        ->orderBy('jml_point', 'desc')
        ->limit(10)
        ->get()->getResultArray();
    } 
    public function getTotalpointsiswarombel($bln,  $id_tapel, $idrombel)
    {
        return $this->db->table($this->table)
        ->select('no_induk, nm_siswa, jml_point, nm_rombel')
        ->join('t_siswa', 't_siswa.id_siswa = t_total_point.id_siswa')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('bln', $bln)
        ->where('t_total_point.id_tapel',  $id_tapel)
        ->where('t_siswa_rombel.id_rombel',  $idrombel)
        ->orderBy('jml_point', 'desc')
        ->get()->getResultArray();
    }
    public function saveTotalpointsiswa($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insertBatch($data);
    }

    public function editTotalpointsiswa($data, $bln,  $id_tapel, $idptk)
    {
        $builder = $this->db->table($this->table);
        $builder->where('bln', $bln);
        $builder->where('id_tapel',  $id_tapel);
        $builder->where('id_siswa', $idptk);
        return $builder->update($data);
    }
    public function hapusTotalpointsiswa($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_total_point' => $id]);
    }
}
