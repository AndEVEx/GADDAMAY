<?php

namespace App\Models;
use CodeIgniter\Database\BaseBuilder;

use CodeIgniter\Model;

class Rombel_model extends Model
{
    protected $table = 't_rombel';

    public function getRombel($id)
    {
        return $this->db->table($this->table)
        ->select('t_rombel.*, r_tingkat_kelas.nm_tingkat_kelas, wali.nama_ptk, bk.nama_ptk as nm_guru_bk')
        ->join('r_tingkat_kelas','r_tingkat_kelas.id_tingkat_kelas = t_rombel.id_tingkat_kelas')
        ->join('t_ptk wali','wali.id_ptk = t_rombel.id_walikelas','LEFT')
        ->join('t_ptk bk','bk.id_ptk = t_rombel.id_guru_bk','LEFT')
        ->where('id_tapel', $id)
        ->orderby('nm_rombel','ASC')
        ->get()->getResultArray();
    }
    public function getRombelwalikelas($id)
    {
        return $this->db->table($this->table)
        ->select('t_rombel.id_rombel as id_rombel,nm_rombel,id_setting_walikelas,nm_walikelas')
        ->join('t_setting_walikelas','t_setting_walikelas.id_rombel = t_rombel.id_rombel')
        ->join('t_walikelas','t_walikelas.id_walikelas = t_setting_walikelas.id_walikelas')
        ->where('t_setting_walikelas.id_tapel', $id)
        ->orderby('nm_rombel','ASC')
        ->get()->getResultArray();
    }
    public function getRombelnonwalikelas($id)
    {
        return $this->db->table($this->table)
        ->WhereNotIn('id_rombel', static function (BaseBuilder $builder) {
            $builder->select('id_rombel')->from('t_setting_walikelas');
        })
        ->where('t_rombel.id_tapel',$id)
        ->get()->getResultArray();
    }
    
    public function saveRombel($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
   
    public function editRombel($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_rombel', $id);
        return $builder->update($data);
    }
    public function hapusRombel($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_rombel' => $id]);
    }

}
