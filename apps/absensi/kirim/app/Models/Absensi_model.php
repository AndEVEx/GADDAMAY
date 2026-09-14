<?php

namespace App\Models;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class Absensi_model extends Model
{
    protected $table = 'tweb_pegawai_hadir';

    public function getAbsensi($id)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nomor_absensi,nip,nama_ptk,JAM,STATUS,nama_jenis_ptk,t_anggota_shift.id_shift as id_shift,jam_masuk')
        ->join('t_ptk', 't_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_ptk.id_ptk = t_anggota_shift.id_ptk')
        ->join('r_shift', 't_anggota_shift.id_shift = r_shift.id_shift')
        ->where('TANGGAL', $id)
        ->where('STATUS', 0)
        ->orderBy('nama_ptk', 'ASC')
        ->get()->getResultArray();
    }
    public function getAbsensipegawai($id)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nomor_absensi,nip,nama_ptk,JAM,STATUS')
        ->join('t_ptk', 't_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK')
        ->where('ID_PEGAWAI', $id)
        ->where('STATUS', 0)
        ->orderBy('TANGGAL', 'DESC')
        ->get()->getResultArray();
    }
    public function getAbsbln($id)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nomor_absensi,nip,nama_ptk,JAM,STATUS,nama_jenis_ptk,t_anggota_shift.id_shift as id_shift,jam_masuk,TANGGAL')
        ->join('t_ptk', 't_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_ptk.id_ptk = t_anggota_shift.id_ptk')
        ->join('r_shift', 't_anggota_shift.id_shift = r_shift.id_shift')
        ->where('MONTH(TANGGAL)', $id)
        ->where('STATUS', 0)
        ->orderBy('nama_ptk', 'ASC')
        ->get()->getResultArray();
    }
    public function getAbsblnshift($id,$shift)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nomor_absensi,nip,nama_ptk,JAM,STATUS,nama_jenis_ptk,t_anggota_shift.id_shift as id_shift,jam_masuk,TANGGAL')
        ->join('t_ptk', 't_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_ptk.id_ptk = t_anggota_shift.id_ptk')
        ->join('r_shift', 't_anggota_shift.id_shift = r_shift.id_shift')
        ->where('MONTH(TANGGAL)', $id)
        ->where('t_anggota_shift.id_shift', $shift)
        ->where('STATUS', 0)
        ->orderBy('nama_ptk', 'ASC')
        ->get()->getResultArray();
    }
    public function getAbsblnjenis($id,$jenis)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nomor_absensi,nip,nama_ptk,JAM,STATUS,nama_jenis_ptk,t_anggota_shift.id_shift as id_shift,jam_masuk,TANGGAL')
        ->join('t_ptk', 't_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_ptk.id_ptk = t_anggota_shift.id_ptk')
        ->join('r_shift', 't_anggota_shift.id_shift = r_shift.id_shift')
        ->where('MONTH(TANGGAL)', $id)
        ->where('t_ptk.id_jenis_ptk', $jenis)
        ->where('STATUS', 0)
        ->orderBy('nama_ptk', 'ASC')
        ->get()->getResultArray();
    }
    public function getAbsblnfilter($id,$jenis,$shift)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nomor_absensi,nip,nama_ptk,JAM,STATUS,nama_jenis_ptk,t_anggota_shift.id_shift as id_shift,jam_masuk,TANGGAL')
        ->join('t_ptk', 't_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_ptk.id_ptk = t_anggota_shift.id_ptk')
        ->join('r_shift', 't_anggota_shift.id_shift = r_shift.id_shift')
        ->where('MONTH(TANGGAL)', $id)
        ->where('t_ptk.id_jenis_ptk', $jenis)
        ->where('t_anggota_shift.id_shift', $shift)
        ->where('STATUS', 0)
        ->orderBy('nama_ptk', 'ASC')
        ->get()->getResultArray();
    }
   
    public function getAbsensilembur($id)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nomor_absensi,nip,nama_ptk,JAM,STATUS,nama_jenis_ptk,t_anggota_shift.id_shift as id_shift,jam_masuk,jam_pulang')
        ->join('t_ptk', 't_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_ptk.id_ptk = t_anggota_shift.id_ptk')
        ->join('r_shift', 't_anggota_shift.id_shift = r_shift.id_shift')
        ->where('TANGGAL', $id)
        ->where('STATUS', 0)
        ->orderBy('nama_ptk', 'ASC')
        ->get()->getResultArray();
    }
    public function getAbslemburshift($id,$shift)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nomor_absensi,nip,nama_ptk,JAM,STATUS,nama_jenis_ptk,t_anggota_shift.id_shift as id_shift,jam_masuk,jam_pulang')
        ->join('t_ptk', 't_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_ptk.id_ptk = t_anggota_shift.id_ptk')
        ->join('r_shift', 't_anggota_shift.id_shift = r_shift.id_shift')
        ->where('t_anggota_shift.id_shift', $shift)
        ->where('TANGGAL', $id)
        ->where('STATUS', 0)
        ->orderBy('nama_ptk', 'ASC')
        ->get()->getResultArray();
    }
    public function getAbslemburjenis($id,$jenis)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nomor_absensi,nip,nama_ptk,JAM,STATUS,nama_jenis_ptk,t_anggota_shift.id_shift as id_shift,jam_masuk,jam_pulang')
        ->join('t_ptk', 't_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_ptk.id_ptk = t_anggota_shift.id_ptk')
        ->join('r_shift', 't_anggota_shift.id_shift = r_shift.id_shift')
        ->where('t_ptk.id_jenis_ptk', $jenis)
        ->where('TANGGAL', $id)
        ->where('STATUS', 0)
        ->orderBy('nama_ptk', 'ASC')
        ->get()->getResultArray();
    }
    public function getAbslembur($id,$jenis,$shift)
    {
        return $this->db->table($this->table)
        ->select('t_ptk.id_ptk as id_ptk,nomor_absensi,nip,nama_ptk,JAM,STATUS,nama_jenis_ptk,t_anggota_shift.id_shift as id_shift,jam_masuk,jam_pulang')
        ->join('t_ptk', 't_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_ptk.id_ptk = t_anggota_shift.id_ptk')
        ->join('r_shift', 't_anggota_shift.id_shift = r_shift.id_shift')
        ->where('t_ptk.id_jenis_ptk', $jenis)
        ->where('t_anggota_shift.id_shift', $shift)
        ->where('TANGGAL', $id)
        ->where('STATUS', 0)
        ->orderBy('nama_ptk', 'ASC')
        ->get()->getResultArray();
    }
    public function getAbsensidetail($id, $tgl)
    {
        return $this->db->table($this->table)
        ->select('nomor_absensi,nip,nama_ptk,JAM,STATUS,nama_jenis_ptk,t_anggota_shift.id_shift as id_shift,jam_masuk')
        ->join('t_ptk', 't_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK')
        ->join('r_jenis_ptk', 'r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk')
        ->join('t_anggota_shift', 't_ptk.id_ptk = t_anggota_shift.id_ptk')
        ->join('r_shift', 't_anggota_shift.id_shift = r_shift.id_shift')
        ->where('TANGGAL', $tgl)
        ->where('ID_PEGAWAI', $id)
        ->orderBy('nama_ptk', 'ASC')
        ->get()->getResultArray();
    }
    public function getAbsensihari($id)
    {
        return $this->db->table($this->table)
        ->select('nama_ptk,JAM,STATUS,id_ptk')
        ->join('t_ptk', 't_ptk.nomor_absensi = tweb_pegawai_hadir.NO_INDUK')
        ->where('TANGGAL', $id)
        ->orderBy('JAM', 'DESC')
        ->limit(10)
        ->get()->getResultArray();
    }
    public function getAbsensifilter($tgl)
    {
        return $this->db->table('t_ptk')
        ->select('t_ptk.id_ptk as id_ptk,nomor_absensi,nip,nama_ptk')
        ->WhereNotIn('id_ptk', static function (BaseBuilder $builder) {
            $builder->select('ID_PEGAWAI')->from('tweb_pegawai_hadir');
        })
        ->join('tweb_pegawai_hadir', 't_ptk.id_ptk = tweb_pegawai_hadir.ID_PEGAWAI')
        ->where('TANGGAL', $tgl)
        ->get()->getResultArray();
    }
   
    public function saveAbsensi($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }
    public function saveAbsensinon($data)
    {
        $builder = $this->db->table('tweb_pegawai_absen');
        return $builder->insert($data);
    }
   
    public function editAbsensi($data, $id, $tgl)
    {
        $builder = $this->db->table($this->table);
        $builder->where('ID_PEGAWAI', $id);
        $builder->where('TANGGAL', $tgl);
        return $builder->update($data);
    }
    public function editAbsensinon($data, $id, $tgl)
    {
        $builder = $this->db->table('tweb_pegawai_absen');
        $builder->where('ID_PEGAWAI', $id);
        $builder->where('TANGGAL_ABSEN', $tgl);
        return $builder->update($data);
    }
    public function hapusAbsensinon($id, $tgl)
    {
        $builder = $this->db->table('tweb_pegawai_absen');
        $builder->where('ID_PEGAWAI', $id);
        $builder->where('TANGGAL_ABSEN', $tgl);
        return $builder->delete();
    }

}
