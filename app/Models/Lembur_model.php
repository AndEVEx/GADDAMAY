<?php

namespace App\Models;

use CodeIgniter\Model;

class Lembur_model extends Model
{
    protected $table = 't_lembur';

    public function getLembur($tgl)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_jenis_ptk as id_jenis_ptk,nip,nik,nama_ptk,nama_jenis_ptk,tgl_lembur,tgl_approve,nama')
        ->join('t_ptk','t_ptk.id_ptk = t_lembur.id_ptk')
        ->join('t_user','t_user.id_user = t_lembur.id_user')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->where('tgl_lembur', $tgl)
        ->get()->getResultArray();
    }
    public function getLemburtgl($tgl1,$tgl2)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_jenis_ptk as id_jenis_ptk,t_lembur.id_ptk as id_ptk,nip,nik,nama_ptk,nama_jenis_ptk,tgl_lembur,tgl_approve,nama')
        ->join('t_ptk','t_ptk.id_ptk = t_lembur.id_ptk')
        ->join('t_user','t_user.id_user = t_lembur.id_user')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->where('tgl_lembur >=', $tgl1)
        ->where('tgl_lembur <=', $tgl2)
        ->get()->getResultArray();
    }
    public function getLemburtglshift($tgl1,$tgl2,$shift)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_jenis_ptk as id_jenis_ptk,t_lembur.id_ptk as id_ptk,nip,nik,nama_ptk,nama_jenis_ptk,tgl_lembur,tgl_approve,nama')
        ->join('t_ptk','t_ptk.id_ptk = t_lembur.id_ptk')
        ->join('t_user','t_user.id_user = t_lembur.id_user')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_anggota_shift.id_ptk = t_ptk.id_ptk')
        ->where('tgl_lembur >=', $tgl1)
        ->where('tgl_lembur <=', $tgl2)
        ->where('id_shift', $shift)
        ->get()->getResultArray();
    }
    public function getLemburtgljenis($tgl1,$tgl2,$jenis)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_jenis_ptk as id_jenis_ptk,t_lembur.id_ptk as id_ptk,nip,nik,nama_ptk,nama_jenis_ptk,tgl_lembur,tgl_approve,nama')
        ->join('t_ptk','t_ptk.id_ptk = t_lembur.id_ptk')
        ->join('t_user','t_user.id_user = t_lembur.id_user')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_anggota_shift.id_ptk = t_ptk.id_ptk')
        ->where('tgl_lembur >=', $tgl1)
        ->where('tgl_lembur <=', $tgl2)
        ->where('t_ptk.id_jenis_ptk', $jenis)
        ->get()->getResultArray();
    }
    public function getLemburtglshiftjenis($tgl1,$tgl2,$jenis,$shift)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_jenis_ptk as id_jenis_ptk,t_lembur.id_ptk as id_ptk,nip,nik,nama_ptk,nama_jenis_ptk,tgl_lembur,tgl_approve,nama')
        ->join('t_ptk','t_ptk.id_ptk = t_lembur.id_ptk')
        ->join('t_user','t_user.id_user = t_lembur.id_user')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_anggota_shift.id_ptk = t_ptk.id_ptk')
        ->where('tgl_lembur >=', $tgl1)
        ->where('tgl_lembur <=', $tgl2)
        ->where('t_ptk.id_jenis_ptk', $jenis)
        ->where('id_shift', $shift)
        ->get()->getResultArray();
    }
    public function saveLembur($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insertBatch($data);
    }

    public function editLembur($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_lembur', $id);
        return $builder->update($data);
    }
    public function hapusLembur($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_lembur' => $id]);
    }
}
