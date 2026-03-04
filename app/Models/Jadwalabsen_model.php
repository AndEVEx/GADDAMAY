<?php

namespace App\Models;

use CodeIgniter\Model;

class Jadwalabsen_model extends Model
{
    protected $table = 'jadwal_absen';

    public function getJadwal()
    {
        return $this->db->table($this->table)
        ->join('t_ptk','t_ptk.id_ptk = libur.id_ptk')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->orderBy('tgl_entri', 'Desc')
        ->get()->getResultArray();
    }
   
    public function saveJadwal($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insertBatch($data);
    }

    public function editJadwal($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->updateBatch($data, ['id_ptk', 'tgl_jadwal']);
    }
    public function hapusJadwal($id,$bln,$thn)
    {
        $builder = $this->db->table($this->table);
        $builder->where('YEAR(tgl_jadwal)', $thn);
        $builder->where('bulan', $bln);
        return $builder->delete(['id_ptk' => $id]);
    }
}
