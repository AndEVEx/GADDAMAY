<?php

namespace App\Models;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class Pegawai_model extends Model
{
    protected $table = 't_ptk';

    public function getPegawai()
    {
        return $this->db->table($this->table)
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->orderBy('nama_ptk')
        ->get()->getResultArray();
    }
    public function getPegawaidetail($id)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_jenis_ptk as id_jenis_ptk,t_ptk.id_ptk as id_ptk,nip,nik,nama_ptk,nama_jenis_ptk,no_hp,nomor_absensi,alamat,tempat_lahir,tgl_lahir')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_anggota_shift.id_ptk = t_ptk.id_ptk')
        ->where('t_ptk.id_ptk',$id)
        ->where('t_ptk.status_ptk',1)
        ->get()->getResultArray();
    }
    public function getPegawaidosen()
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_jenis_ptk as id_jenis_ptk,t_ptk.id_ptk as id_ptk,nip,nama_ptk,nama_jenis_ptk')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_anggota_shift.id_ptk = t_ptk.id_ptk')
        ->where('t_ptk.id_jenis_ptk',1)
        ->get()->getResultArray();
    }
    public function getPegawaipil($jenis)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_jenis_ptk as id_jenis_ptk,t_ptk.id_ptk as id_ptk,nip,nama_ptk,nama_jenis_ptk')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_anggota_shift.id_ptk = t_ptk.id_ptk')
        ->where('t_ptk.id_jenis_ptk',$jenis)
        ->get()->getResultArray();
    }
    public function getPegawaiall()
    { 
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nama_ptk,nomor_absensi,nip,nama_jenis_ptk')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_anggota_shift.id_ptk = t_ptk.id_ptk')
        ->where('status_ptk', 1)
        ->get()->getResultArray();
    }
    public function getPegshift($shift)
    { 
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nama_ptk,nomor_absensi,nip,nama_jenis_ptk')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_anggota_shift.id_ptk = t_ptk.id_ptk')
        ->where('status_ptk', 1)
        ->where('id_shift', $shift)
        ->get()->getResultArray();
    }
    public function getPegawaijenis($id)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nama_ptk,nomor_absensi,nip,nama_jenis_ptk')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_anggota_shift.id_ptk = t_ptk.id_ptk')
        ->where('t_ptk.id_jenis_ptk', $id)
        ->where('status_ptk', 1)
        ->get()->getResultArray();
    }
    public function getPegfilter($jenis,$shift)
    { 
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nama_ptk,nomor_absensi,nip,nama_jenis_ptk')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_anggota_shift.id_ptk = t_ptk.id_ptk')
        ->where('status_ptk', 1)
        ->where('id_shift', $shift)
        ->where('t_ptk.id_jenis_ptk', $jenis)
        ->get()->getResultArray();
    }
    public function getPegawaishift()
    {
        return $this->db->table($this->table)
        ->WhereNotIn('id_ptk', static function (BaseBuilder $builder) {
            $builder->select('id_ptk')->from('t_anggota_shift');
        })
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->get()->getResultArray();
    }

    public function savePegawai($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }

    public function editPegawai($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_ptk', $id);
        return $builder->update($data);
    }
    public function hapusPegawai($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_ptk' => $id]);
    }
}
