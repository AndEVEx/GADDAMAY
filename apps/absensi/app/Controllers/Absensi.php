<?php

namespace App\Controllers;
use App\Models\Absensi_model;
use App\Models\Jenisketenagaan_model;
use App\Models\Pegawai_model;
use App\Models\Shift_model;
use App\Models\Absen_model;

use CodeIgniter\Controller;

class Absensi extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensi_model;
        $m_jenis = new Jenisketenagaan_model;
        $m_pegawai = new Pegawai_model;
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi'
        );

        if(empty($this->request->getPost('jenis'))){
            $tgl = date('Y-m-d');
            $data = array(
                'getAbsensi' => $model->getAbsensi($tgl),
                'getJenis' => $m_jenis->getJenis(),
                'getTanggal' => $tgl,
                'Shift' => "",
                'Jenis' => ""
            );
        }else{
            $tgl = $this->request->getPost('tgl');
            $jenis = $this->request->getPost('jenis');
            if($jenis=="All"){
                $data = array(
                    'getAbsensi' => $m_pegawai->getPegawaiall(),
                    'getJenis' => $m_jenis->getJenis(),
                    'getTanggal' => $tgl,
                    'Jenis' => $jenis
                );
            }else{
                $data = array(
                    'getAbsensi' => $m_pegawai->getPegfilter($jenis),
                    'getJenis' => $m_jenis->getJenis(),
                    'getTanggal' => $tgl,
                    'Jenis' => $jenis
                );
            }
        }
 
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/harian', $data);
        echo view('index/footer');
    }
    public function cetakharian()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensi_model;
        $m_jenis = new Jenisketenagaan_model;
        
        $m_pegawai = new Pegawai_model;
        
        $tgl = $this->request->getVar('tgl');
        $jenis = $this->request->getVar('jenis');
        if($jenis=="All"){
            $data = array(
                'getAbsensi' => $m_pegawai->getPegawaiall(),
                'getJenis' => $m_jenis->getJenis(),
                'getTanggal' => $tgl,
                'Jenis' => $jenis
            );
        }else{
            $data = array(
                'getAbsensi' => $m_pegawai->getPegfilter($jenis),
                'getJenis' => $m_jenis->getJenis(),
                'getTanggal' => $tgl,
                'Jenis' => $jenis
            );
        }

        echo view('func');
        echo view('print/harian', $data);
    }
    public function pertanggal()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_jenis = new Jenisketenagaan_model;
       
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi/pertanggal'
        );

        if(empty($this->request->getPost('jenis'))){
            $tgl1 = date('Y-m-d');
            $tgl2 = date('Y-m-d');
            $data = array(
                'getPegawai' => $model->getPegawai(),
                'getJenis' => $m_jenis->getJenis(),
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'Jenis' => 0,
                'Shift' => 0
            );
        }else{
            $tgl1 = $this->request->getPost('tgl1');
            $tgl2 = $this->request->getPost('tgl2');
            $jenis = $this->request->getPost('jenis');
        
            if($jenis=="All"){
                $data = array(
                    'getPegawai' => $model->getPegawai(),
                    'getJenis' => $m_jenis->getJenis(),
                    'getTanggal1' => $tgl1,
                    'getTanggal2' => $tgl2,
                    'Jenis' => 0,
                    'Shift' => 0
                );
            }else{
                $data = array(
                    'getPegawai' => $model->getPegfilter($jenis),
                    'getJenis' => $m_jenis->getJenis(),
                    'getTanggal1' => $tgl1,
                    'getTanggal2' => $tgl2,
                    'Jenis' => $jenis
                );
            }
        }

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/pertanggal', $data);
        echo view('index/footer');
    }
    public function cetakpertanggal()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_jenis = new Jenisketenagaan_model;
       
       
        $tgl1 = $this->request->getVar('tgl1');
        $tgl2 = $this->request->getVar('tgl2');
        $jenis = $this->request->getVar('jen');
    
        if($jenis==0){
            $data = array(
                'getPegawai' => $model->getPegawai(),
                'getJenis' => $m_jenis->getJenis(),
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'Jenis' => 0,
                'Shift' => 0
            );
        }else{
            $data = array(
                'getPegawai' => $model->getPegfilter($jenis),
                'getJenis' => $m_jenis->getJenis(),
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'Jenis' => $jenis
            );
        }
 
        echo view('func');
        echo view('print/pertanggal', $data);

    }
    public function pertanggaluser()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_absensi = new Absensi_model;

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi/pertanggaluser'
        );

        if(empty($this->request->getPost('id_ptk'))){
            $tgl1 = date('Y-m-d');
            $tgl2 = date('Y-m-d');
            $data = array(
                'getPegawai' => $model->getPegawai(),
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'idPtk' => 0,
                'nama' => ""
            );
        }else{
            $tgl1 = $this->request->getPost('tgl1');
            $tgl2 = $this->request->getPost('tgl2');
            $id_ptk = $this->request->getPost('id_ptk');

            //ambil nama pegawai
            $db = \Config\Database::connect();
            $query = $db->query("SELECT nama_ptk FROM t_ptk where id_ptk='$id_ptk'");
            $row = $query->getRow();
            $nm = $row->nama_ptk;

            $data = array(
                'getPegawai' => $model->getPegawai(),
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'getAbsensi' => $m_absensi->getAbsensitgluser($id_ptk, $tgl1, $tgl2),
                'idPtk' => $id_ptk,
                'nama' => $nm
            );
          
        }

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/pertanggaluser', $data);
        echo view('index/footer');
    }
    public function cetakpertanggaluser()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_absensi = new Absensi_model;

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi/pertanggaluser'
        );

       
            $tgl1 = $this->request->getVar('tgl1');
            $tgl2 = $this->request->getVar('tgl2');
            $id_ptk = $this->request->getVar('id_ptk');

            //ambil nama pegawai
            $db = \Config\Database::connect();
            $query = $db->query("SELECT nama_ptk FROM t_ptk where id_ptk='$id_ptk'");
            $row = $query->getRow();
            $nm = $row->nama_ptk;

            $data = array(
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'getAbsensi' => $m_absensi->getAbsensitgluser($id_ptk, $tgl1, $tgl2),
                'idPtk' => $id_ptk,
                'nama' => $nm
            );
          
        echo view('func');
        echo view('print/pertanggaluser', $data);
    }
    public function bulanan()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_jenis = new Jenisketenagaan_model;
      
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi/bulanan'
        );

        if(empty($this->request->getPost('jenis'))){
            $bln = date('m');
            $data = array(
                'getPegawai' => $model->getPegawai(),
                'getJenis' => $m_jenis->getJenis(),
                'getBulan' => $bln,
                'Jenis' => 0,
                'Shift' => 0
            );
        }else{ 
            $bln = $this->request->getPost('bln');
            $jenis = $this->request->getPost('jenis');
            if($jenis=="All"){
                $data = array(
                    'getPegawai' => $model->getPegawai(),
                    'getJenis' => $m_jenis->getJenis(),                  
                    'getBulan' => $bln,
                    'Jenis' => 0
                );
            }else{
                $data = array(
                    'getPegawai' => $model->getPegfilter($jenis),
                    'getJenis' => $m_jenis->getJenis(),
                    'getBulan' => $bln,
                    'Jenis' => $jenis
                );
            }
        }
        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/bulanan', $data);
        echo view('index/footer');
    }
    
    public function cetakbulanan()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_jenis = new Jenisketenagaan_model;
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi'
        );

        
        $bln = $this->request->getVar('bln');
        $jenis = $this->request->getVar('jen');
        if($jenis==0){
            $data = array(
                'getPegawai' => $model->getPegawai(),
                'getJenis' => $m_jenis->getJenis(),                  
                'getBulan' => $bln,
                'Jenis' => 0
            );
        }else{
            $data = array(
                'getPegawai' => $model->getPegfilter($jenis),
                'getJenis' => $m_jenis->getJenis(),
                'getBulan' => $bln,
                'Jenis' => $jenis
            );
        }

        echo view('func');
        echo view('print/bulanan', $data);
    }
    public function pegawai()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensi_model;
        $id = session()->get('id_user');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi/pegawai'
        );

        if(empty($this->request->getPost('bln'))){
            $data = array(
                'getBulan' => date('m'),
                'getId' => $id
            );
        }else{
            $bln = $this->request->getPost('bln');
            $data = array(
                'getBulan' => $bln,
                'getId' => $id
            );
        }

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/absenpegawai', $data);
        echo view('index/footer');
    }
    public function perpegawai()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $db = \Config\Database::connect();
        $model = new Absensi_model;
        $m_pegawai = new Pegawai_model;
        $tgl = date('Y-m-d');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi Perpegawai',
            'nav' => 'Absensi/perpegawai'
        );

        if(empty($this->request->getPost('id_ptk'))){
            $data = array(
                'getAbsensi' => $model->getAbsensi($tgl),
                'getPegawai' => $m_pegawai->getPegawaiall(),
                'getNama' => "",
                'getJenisptk' => "",
                'getId' => ""
            );
        }else{
            $id_ptk = $this->request->getPost('id_ptk');
            //ambil data pegawai
            $query = $db->query("SELECT nama_ptk,nama_jenis_ptk FROM t_ptk JOIN r_jenis_ptk ON r_jenis_ptk.id_jenis_ptk = t_ptk.id_jenis_ptk where id_ptk='$id_ptk'");
            $row = $query->getRow();

            $data = array(
                'getAbsensi' => $model->getAbsensidetail($id_ptk, $tgl),
                'getPegawai' => $m_pegawai->getPegawaiall(),
                'getNama' => $row->nama_ptk,
                'getJenisptk' => $row->nama_jenis_ptk,
                'getId' => $id_ptk,
                
            );
        }
         
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/perpegawai', $data);
        echo view('index/footer');
    }
    public function koreksi()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensi_model;
        $m_pegawai = new Pegawai_model;
        $m_jenis = new Jenisketenagaan_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Koreksi Data Absensi',
            'nav' => 'Absensi/koreksi'
        );

        if(empty($this->request->getVar('jenis'))){
            $tgl = date('Y-m-d');
            $data = array(
                'getPegawai' => $m_pegawai->getPegawaiall(),
                'getJenis' => $m_jenis->getJenis(),
                'getTanggal' => $tgl,
                'jenis' => ""
            );
        }elseif($this->request->getVar('jenis')=="all"){
            $tgl = $this->request->getVar('tgl');
            $jenis = $this->request->getVar('jenis');
            $data = array(
                'getPegawai' => $m_pegawai->getPegawaiall(),
                'getJenis' => $m_jenis->getJenis(),
                'getTanggal' => $tgl,
                'jenis' => $jenis
            );
        }else{
            $tgl = $this->request->getVar('tgl');
            $jenis = $this->request->getVar('jenis');
            $data = array(
                'getPegawai' => $m_pegawai->getPegawaijenis($jenis),
                'getJenis' => $m_jenis->getJenis(),
                'getTanggal' => $tgl,
                'jenis' => $jenis
            );
        }
        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/koreksi', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        
        $model = new Jenisketenagaan_model;
        $data = array(
            'nama_jenis_ptk' => $this->request->getPost('nama')
        );

        //validasi input
        if(!$this->validate([
            "nama" => 'required|is_unique[r_jenis_ptk.nama_jenis_ptk]'
        ])){
            session()->setFlashdata('error','Ditambahkan, Nama Jenis Kerja tidak boleh sama');
            return redirect()->to('/Jeniskerja');
        }

        $success = $model->saveJenis($data);
        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Jeniskerja');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Jeniskerja');
        }
        
    }
    
    public function updatemasuk()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensi_model;
        $tgl = $this->request->getPost('tgl');
        $jenis = $this->request->getPost('jenis');
        
        if(empty($this->request->getPost('sts'))){
            $data = array(
                'JAM' => $this->request->getPost('jam')
            );
        }else{
            $data = array(
                'NO_INDUK' => $this->request->getPost('nomor_absensi'),
                'ID_PEGAWAI' => $this->request->getPost('id'),
                'TANGGAL' => $this->request->getPost('tgl'),
                'JAM' => $this->request->getPost('jam'),
                'STATUS' => $this->request->getPost('status')
            );
            //insert data
            $success = $model->saveAbsensi($data);
        }

        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Absensi/koreksi/?tgl='.$tgl.'&jenis='.$jenis);
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Absensi/koreksi/?tgl='.$tgl.'&jenis='.$jenis);
        }
    }
    public function updatetidakmasuk()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensi_model;
        
        if(($this->request->getPost('status'))=="Alpha"){
            $data = array(
                'NO_INDUK' => $this->request->getPost('nomor_absensi'),
                'ID_PEGAWAI' => $this->request->getPost('id'),
                'TANGGAL_ABSEN' => $this->request->getPost('tgl'),
                'STATUS' => $this->request->getPost('sts'),
                'KETERANGAN' => $this->request->getPost('keterangan'),
                'TANGGAL_AKSES' => date('Y-m-d H:i:s')
            );
            //insert data
            $success = $model->saveAbsensinon($data);
        }else{
            $id = $this->request->getPost('id');
            $tgl = $this->request->getPost('tgl');

            //cek apakah dia ingin merubah ke alpha
            if(($this->request->getPost('sts'))==4){
                $success = $model->hapusAbsensinon($id, $tgl);
            }else{
                $data = array(
                    'STATUS' => $this->request->getPost('sts'),
                    'KETERANGAN' => $this->request->getPost('keterangan'),
                );
                //insert data
                $success = $model->editAbsensinon($data, $id, $tgl);
            }
        }

        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Absensi/koreksi');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Absensi/koreksi');
        }
    }
    public function hapus()
    {
        $model = new Jenisketenagaan_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusJenis($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Jeniskerja');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Jeniskerja');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Jeniskerja');
        }
    } 
    public function chart()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Chart Absensi',
            'nav1' => 'Home'
        );
        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/chart');
        echo view('index/footer');
        echo view('home/chart'); 
    }
    public function hadir()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensi_model;

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi Hadir',
            'nav' => 'Absensi/hadir'
        );

        $tgl = date('Y-m-d');
        $data = array(
            'getAbsensi' => $model->getAbsensi($tgl),
            'getTanggal' => $tgl
        );

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/harianhadirguru', $data);
        echo view('index/footer');
    }
    public function pulang()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensi_model;

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi Hadir',
            'nav' => 'Absensi/hadir'
        );

        $tgl = date('Y-m-d');
        $data = array(
            'getAbsensi' => $model->getAbsensipulang($tgl),
            'getTanggal' => $tgl
        );

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/harianpulangguru', $data);
        echo view('index/footer');
    }
    public function terlambat()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensi_model;

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi Terlambat',
            'nav' => 'Absensi/terlambat'
        );
 
        //cek jam masuk
        echo view('func');
        $today = hari_ini();
       
        $jam = jammasukhari($today);
        $tgl = date('Y-m-d');
        $data = array(
            'getAbsensi' => $model->getAbsensi($tgl),
            'getTanggal' => $tgl
        );
  
        echo view('index/sidebar');
        echo view('index/navbar',  $datanav);
        echo view('report/harianterlambatguru', $data);
        echo view('index/footer');
    }
    public function alpha()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensi_model;

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi Alpha',
            'nav' => 'Absensi/alpha'
        );

        $tgl = date('Y-m-d');
        
        $data = array(
            'getTanggal' => $tgl
        );

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/harianalphaguru', $data);
        echo view('index/footer');
    }
    
}
