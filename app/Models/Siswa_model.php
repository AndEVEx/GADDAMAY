<?php

namespace App\Models;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class Siswa_model extends Model
{
    protected $table = 't_siswa';
    protected $primaryKey = 'id_siswa';
    protected $returnType = 'array';
    protected $allowedFields = ['no_induk', 'nm_siswa', 'nisn', 'rfid', 'jk', 'tempat_lahir', 'tgl_lahir', 'alamat', 'hp', 'file', 'sts_siswa'];

    public function getSiswa()
    {
        return $this->db->table($this->table)
        ->orderBy('nm_siswa')
        ->get()->getResultArray();
    }
    public function getSiswafilterkelas($id_tapel)
    {
        return $this->db->table($this->table)
        ->select('t_siswa.id_siswa as id_siswa,no_induk,nm_siswa')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa.id_siswa')
        ->where('sts_siswa', 1)
        ->where('id_tapel', $id_tapel)
        ->get()->getResultArray();
    }
    public function getSiswaid($id)
    {
        return $this->db->table($this->table)
        ->select('t_siswa.id_siswa as id_siswa,no_induk,nm_siswa,nm_rombel,file,nisn,alamat,tempat_lahir,tgl_lahir,hp')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('t_siswa.id_siswa', $id)
        ->get()->getResultArray();
    }

    public function getSiswaall($tapel)
    {
        return $this->db->table($this->table)
        ->select('t_siswa.id_siswa as id_siswa,no_induk,nm_siswa,nm_rombel,file,tempat_lahir,tgl_lahir,nisn,alamat')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('t_siswa_rombel.id_tapel', $tapel)
        ->orderby('nm_siswa','Asc')
        ->get()->getResultArray();
    }
    
    public function getSiswaidtapel($id,$tapel)
    {
        return $this->db->table($this->table)
        ->select('t_siswa.id_siswa as id_siswa,no_induk,nm_siswa,nm_rombel,file,tempat_lahir,tgl_lahir,nisn,alamat')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('t_siswa.id_siswa', $id)
        ->where('t_siswa_rombel.id_tapel',$tapel)
        ->get()->getResultArray();
    }
    
    public function getSiswarombel($id)
    {
        return $this->db->table($this->table)
        ->select('t_siswa.id_siswa as id_siswa,no_induk,nm_siswa,nm_rombel,file')
        ->join('t_siswa_rombel', 't_siswa_rombel.id_siswa = t_siswa.id_siswa')
        ->join('t_rombel', 't_rombel.id_rombel = t_siswa_rombel.id_rombel')
        ->where('t_siswa_rombel.id_rombel', $id)
        ->get()->getResultArray();
    }
    public function getSiswasnonrombel()
    {
        return $this->db->table($this->table)
        ->WhereNotIn('id_siswa', static function (BaseBuilder $builder) {
            $builder->select('id_siswa')->from('t_siswa_rombel');
        })
        ->get()->getResultArray();
    }
    public function saveSiswa($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }

    public function editSiswa($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_siswa', $id);
        return $builder->update($data);
    }
    public function editSiswaimport($data, $nis)
    {
        $builder = $this->db->table($this->table);
        $builder->where('no_induk', $nis);
        return $builder->update($data);
    }
    public function hapusSiswa($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_siswa' => $id]);
    }
}
