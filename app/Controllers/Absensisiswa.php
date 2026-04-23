<?php

namespace App\Controllers;
use App\Models\Absensisiswa_model;
use App\Models\Absensiswa_model;
use App\Models\Siswa_model;
use App\Models\Rombel_model;
use App\Models\Siswarombel_model;

use CodeIgniter\Controller;

class Absensisiswa extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensisiswa_model;
        $m_siswa = new Siswa_model;
        $m_rombel = new Rombel_model;
        $m_siswarombel = new Siswarombel_model;
        $id_tapel = session()->get('id_tapel');
        $id_rombel = $this->request->getPost('id_rombel');
        
        echo view('func_siswa');

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi'
        );

        if(empty($this->request->getPost('id_rombel'))){
            $tgl = date('Y-m-d');
            $data = array(
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getTanggal' => $tgl,
                'idRombel' => $id_rombel,
                'nmRombel' => ""
            );
        }else{
            $tgl = $this->request->getPost('tgl');
            $data = array(
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getTanggal' => $tgl,
                'idRombel' => $id_rombel,
                'nmRombel' => nmrombel($id_rombel)
            );
        }
 
        echo view('index/sidebar');
        
        echo view('index/navbar',  $datanav);
        echo view('report/hariansiswa', $data);
        echo view('index/footer');
    }
    public function cetakharian()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensisiswa_model;
        $m_siswa = new Siswa_model;
        $m_rombel = new Rombel_model;
        $m_siswarombel = new Siswarombel_model;
        $id_tapel = session()->get('id_tapel');
        $id_rombel = $this->request->getVar('id');
        
        echo view('func_siswa');

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi'
        );

        $tgl = $this->request->getVar('tgl');
        $data = array(
            'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
            'getRombel' => $m_rombel->getRombel($id_tapel),
            'getTanggal' => $tgl,
            'idRombel' => $id_rombel,
            'nmRombel' => nmrombel($id_rombel)
        );
       
        echo view('print/hariansiswa', $data);
    }
    public function hadir()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensisiswa_model;

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi Hadir',
            'nav' => 'Absensi/hadir'
        );

        $tgl = date('Y-m-d');
        $data = array(
            'getAbsensi' => $model->getAbsensihadir($tgl),
            'getTanggal' => $tgl
        );

        echo view('index/sidebar');
        echo view('func_siswa');
        echo view('index/navbar',  $datanav);
        echo view('report/harianhadir', $data);
        echo view('index/footer');
    }
    public function pulang()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensisiswa_model;

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
        echo view('func_siswa');
        echo view('index/navbar',  $datanav);
        echo view('report/harianpulang', $data);
        echo view('index/footer');
    }
    public function terlambat()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensisiswa_model;

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi Terlambat',
            'nav' => 'Absensi/terlambat'
        );
 
        //cek jam masuk
        echo view('func_siswa');
        $today = hari_ini();
       
        $jam = jammasukhari($today);
        $tgl = date('Y-m-d');
        $data = array(
            'getAbsensi' => $model->getAbsensiterlambat($tgl,$jam),
            'getTanggal' => $tgl
        );
 
        echo view('index/sidebar');
       
        echo view('index/navbar',  $datanav);
        echo view('report/harianterlambat', $data);
        echo view('index/footer');
    }
    public function alpha()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensisiswa_model;

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
        echo view('func_siswa');
        echo view('index/navbar',  $datanav);
        echo view('report/harianalpha', $data);
        echo view('index/footer');
    }
    public function pertanggal()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswa_model;
        $m_rombel = new Rombel_model;
        $m_siswarombel = new Siswarombel_model;
        $id_tapel = session()->get('id_tapel');
        $id_rombel = $this->request->getPost('id_rombel');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi/pertanggal'
        );
        echo view('func_siswa');
        if(empty($this->request->getPost('id_rombel'))){
            $tgl1 = date('Y-m-d');
            $tgl2 = date('Y-m-d');
            $data = array(
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'idRombel' => $id_rombel,
                'nmRombel' => ""
            );
        }else{
            $tgl1 = $this->request->getPost('tgl1');
            $tgl2 = $this->request->getPost('tgl2');
           
            $data = array(
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'idRombel' => $id_rombel,
                'nmRombel' => nmrombel($id_rombel)
            );
           
        }

        echo view('index/sidebar');
        echo view('index/navbar',  $datanav);
        echo view('report/pertanggalsis', $data);
        echo view('index/footer');
    }
    public function pertanggalsiswa()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswa_model;
        $m_absensi = new Absensisiswa_model;
        $id_tapel = session()->get('id_tapel');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensisiswa/pertanggaluser'
        );

        if(empty($this->request->getPost('id_siswa'))){
            $tgl1 = date('Y-m-d');
            $tgl2 = date('Y-m-d');
            $data = array(
                'getSiswa' => $model->getSiswafilterkelas($id_tapel),
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'idSiswa' => 0,
                'nama' => ""
            ); 
        }else{
            $tgl1 = $this->request->getPost('tgl1');
            $tgl2 = $this->request->getPost('tgl2');
            $id_siswa = $this->request->getPost('id_siswa');

            //ambil nama siswa
            $db = \Config\Database::connect();
            $query = $db->query("SELECT nm_siswa FROM t_siswa where id_siswa='$id_siswa'");
            $row = $query->getRow();
            $nm = $row->nm_siswa;

            $data = array(
                'getSiswa' => $model->getSiswafilterkelas($id_tapel),
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'getAbsensisiswa' => $m_absensi->getAbsensisiswatgl($id_siswa, $tgl1, $tgl2),
                'idSiswa' => $id_siswa,
                'nama' => $nm
            );
          
        }

        echo view('index/sidebar');
        echo view('func_siswa');
        echo view('index/navbar',  $datanav);
        echo view('report/pertanggalusersiswa', $data);
        echo view('index/footer');
    }
    public function bulanan()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswa_model;
        $m_rombel = new Rombel_model;
        $m_siswarombel = new Siswarombel_model;
        $id_tapel = session()->get('id_tapel');
        $id_rombel = $this->request->getPost('id_rombel');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi/bulanan'
        );

        echo view('func_siswa');
        if(empty($this->request->getPost('id_rombel'))){
            $bln = date('m');
            $data = array(
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getBulan' => $bln,
                'idRombel' => $id_rombel,
                'nmRombel' => ""
            );
        }else{
            $bln = $this->request->getPost('bln');
            
            $data = array(
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getBulan' => $bln,
                'idRombel' => $id_rombel,
                'nmRombel' => nmrombel($id_rombel)
            );
            
        }
   
        echo view('index/sidebar');
        echo view('index/navbar',  $datanav);
        echo view('report/bulanansis', $data);
        echo view('index/footer');
    }
    public function cetakpertanggalsiswa()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswa_model;
        $m_absensi = new Absensisiswa_model;
        $id_tapel = session()->get('id_tapel');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi'
        );

            $tgl1 = $this->request->getVar('tgl1');
            $tgl2 = $this->request->getVar('tgl2');
            $id_siswa = $this->request->getVar('id_siswa');

            //ambil nama siswa
            $db = \Config\Database::connect();
            $query = $db->query("SELECT nm_siswa FROM t_siswa where id_siswa='$id_siswa'");
            $row = $query->getRow();
            $nm = $row->nm_siswa;

            $data = array(
                'getSiswa' => $model->getSiswafilterkelas($id_tapel),
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'getAbsensisiswa' => $m_absensi->getAbsensisiswatgl($id_siswa, $tgl1, $tgl2),
                'idSiswa' => $id_siswa,
                'nama' => $nm
            );

        echo view('func_siswa');
        echo view('print/pertanggalusersiswa', $data);

    }
    public function cetakpertanggal()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswa_model;
        $m_rombel = new Rombel_model;
        $m_siswarombel = new Siswarombel_model;
        $id_tapel = session()->get('id_tapel');
        $id_rombel = $this->request->getVar('idrombel');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi/pertanggal'
        );

        
        $tgl1 = $this->request->getVar('tgl1');
        $tgl2 = $this->request->getVar('tgl2');
        echo view('func_siswa');
           
        $data = array(
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'idRombel' => $id_rombel,
                'nmRombel' => nmrombel($id_rombel)
        );

        echo view('print/pertanggalsiswa', $data);

    }
    public function cetakbulanan()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswa_model;
        $m_rombel = new Rombel_model;
        $m_siswarombel = new Siswarombel_model;
        $id_tapel = session()->get('id_tapel');
        $id_rombel = $this->request->getVar('idrombel');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi/bulanan'
        );

        echo view('func_siswa');
        
        $bln = $this->request->getVar('bln');
            
        $data = array(
            'getRombel' => $m_rombel->getRombel($id_tapel),
            'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
            'getBulan' => $bln,
            'idRombel' => $id_rombel,
            'nmRombel' => nmrombel($id_rombel)
        );

        echo view('print/bulanansiswa', $data);
    }
    
    public function persiswa()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $db = \Config\Database::connect();
        $model = new Absensisiswa_model;
        $m_siswa = new Siswa_model;
        $tgl = date('Y-m-d');
        $id_tapel = session()->get('id_tapel');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi Persiswa',
            'nav' => 'Absensi/persiswa'
        );

        if($this->request->getPost('id_siswa')==0){
            $data = array(
                'getSiswa' => $m_siswa->getSiswafilterkelas($id_tapel),
                'getNama' => "",
                'nmRombel' => "",
                'idSiswa' => ""
            );
        }else{
            $id_siswa = $this->request->getPost('id_siswa');
            //ambil data siswa
            $query = $db->query("SELECT no_induk,nm_siswa,nm_rombel FROM t_siswa 
            JOIN t_siswa_rombel ON t_siswa_rombel.id_siswa = t_siswa.id_siswa
            JOIN t_rombel ON t_rombel.id_rombel = t_siswa_rombel.id_rombel
            where t_siswa.id_siswa='$id_siswa' and t_siswa_rombel.id_tapel='$id_tapel'");
            $row = $query->getRow();

            $data = array(
                'getAbsensisiswa' => $model->getAbsensisiswa($id_siswa, $tgl),
                'getSiswa' => $m_siswa->getSiswafilterkelas($id_tapel),
                'getNama' => $row->nm_siswa,
                'nmRombel' => $row->nm_rombel,
                'idSiswa' => $id_siswa,
                
            );
        }
          
        echo view('index/sidebar');
        echo view('func_siswa');
        echo view('index/navbar',  $datanav);
        echo view('report/persiswa', $data);
        echo view('index/footer');
    }
    public function koreksi()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensisiswa_model;
        $m_siswa = new Siswa_model;
        $m_rombel = new Rombel_model;
        $m_siswarombel = new Siswarombel_model;
        $id_tapel = session()->get('id_tapel');
        $id_rombel = $this->request->getVar('id_rombel');
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi'
        );

        if(empty($this->request->getVar('id_rombel'))){
            $tgl = date('Y-m-d');
            $data = array(
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getTanggal' => $tgl,
                'idRombel' => $id_rombel
            );
        }else{
            $tgl = $this->request->getVar('tgl');

            $data = array(
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getTanggal' => $tgl,
                'idRombel' => $id_rombel
            );
        }
        
         
        echo view('index/sidebar');
        echo view('func_siswa');
        echo view('index/navbar',  $datanav);
        echo view('master/koreksisiswa', $data);
        echo view('index/footer');
    }
   
    
    public function updatemasuk()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensisiswa_model;
        $tgl = $this->request->getPost('tgl');
        $id_rombel = $this->request->getPost('id_rombel');

        if(empty($this->request->getPost('sts'))){
            $data = array(
                'jam' => $this->request->getPost('jam')
            );
        }else{
            $data = array(
                'id_siswa' => $this->request->getPost('id_siswa'),
                'tgl_hadir' => $this->request->getPost('tgl'),
                'jam' => $this->request->getPost('jam'),
                'id_tapel' => session()->get('id_tapel'),
                'sts_hadir' => $this->request->getPost('status')
            );
            //insert data
            $success = $model->saveAbsensi($data);
        }

        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Absensisiswa/koreksi/?id_rombel='.$id_rombel.'&tgl='.$tgl);
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Absensisiswa/koreksi/?id_rombel='.$id_rombel.'&tgl='.$tgl);
        }
    }
    public function updatetidakmasuk()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensiswa_model;
        $tgl = $this->request->getPost('tgl');
        $id_rombel = $this->request->getPost('id_rombel');
        
        if(($this->request->getPost('status'))=="Alpha"){
            $data = array(
                'id_siswa' => $this->request->getPost('id_siswa'),
                'tgl_absen' => $this->request->getPost('tgl'),
                'sts_absen' => $this->request->getPost('sts'),
                'ket_absen' => $this->request->getPost('keterangan'),
                'id_tapel' => session()->get('id_tapel'),
                'tgl_entri' => date('Y-m-d H:i:s'),
                'sts_approve' => 1
            );
            //insert data
            $success = $model->saveAbsen($data);
        }else{
            $id = $this->request->getPost('id');
            $tgl = $this->request->getPost('tgl');

            //cek apakah dia ingin merubah ke alpha
            if(($this->request->getPost('sts'))==4){
                $success = $model->hapusAbsen($id, $tgl);
            }else{
                $data = array(
                    'sts_absen' => $this->request->getPost('sts'),
                    'ket_absen' => $this->request->getPost('keterangan'),
                );
                //insert data
                $success = $model->editAbsensiswa($data, $id, $tgl);
            }
        }

        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Absensisiswa/koreksi/?id_rombel='.$id_rombel.'&tgl='.$tgl);
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Absensisiswa/koreksi/?id_rombel='.$id_rombel.'&tgl='.$tgl);
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
        echo view('func_siswa');
        echo view('index/navbar',  $datanav);
        echo view('report/chartsiswa');
        echo view('index/footer');
        echo view('home/chartsiswa'); 
    }

    public function koreksiwali()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensisiswa_model;
        $m_siswa = new Siswa_model;
        $m_rombel = new Rombel_model;
        $m_siswarombel = new Siswarombel_model;
        $id_tapel = session()->get('id_tapel');
        echo view('func_siswa');
        $id_walikelas = session()->get('id_user');
        $id_rombel = rombelwalikelas($id_walikelas,$id_tapel);
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Absensi'
        );

        if(empty($this->request->getVar('tgl'))){
            $tgl = date('Y-m-d');
        }else{
            $tgl = $this->request->getVar('tgl');
        }
        

        $data = array(
            'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
            'getTanggal' => $tgl,
            'idRombel' => $id_rombel
        );
        

        echo view('index/sidebar');
        echo view('index/navbar',  $datanav);
        echo view('master/koreksiwali', $data);
        echo view('index/footer');
    }
    public function updatemasukwali()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensisiswa_model;
        $tgl = $this->request->getPost('tgl');

        if(empty($this->request->getPost('sts'))){
            $data = array(
                'jam' => $this->request->getPost('jam')
            );
        }else{
            $data = array(
                'id_siswa' => $this->request->getPost('id_siswa'),
                'tgl_hadir' => $this->request->getPost('tgl'),
                'jam' => $this->request->getPost('jam'),
                'id_tapel' => session()->get('id_tapel'),
                'sts_hadir' => $this->request->getPost('status')
            );
            //insert data
            $success = $model->saveAbsensi($data);
        }

        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Absensisiswa/koreksiwali/?tgl='.$tgl);
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Absensisiswa/koreksiwali/?tgl='.$tgl);
        }
    }
   
    public function updatetidakmasukwali()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensiswa_model;
        $tgl = $this->request->getPost('tgl');
        $id_rombel = $this->request->getPost('id_rombel');
        
        if(($this->request->getPost('status'))=="Alpha"){
            $data = array(
                'id_siswa' => $this->request->getPost('id_siswa'),
                'tgl_absen' => $this->request->getPost('tgl'),
                'sts_absen' => $this->request->getPost('sts'),
                'ket_absen' => $this->request->getPost('keterangan'),
                'id_tapel' => session()->get('id_tapel'),
                'tgl_entri' => date('Y-m-d H:i:s'),
                'sts_approve' => 1
            );
            //insert data
            $success = $model->saveAbsen($data);
        }else{
            $id = $this->request->getPost('id');
            $tgl = $this->request->getPost('tgl');

            //cek apakah dia ingin merubah ke alpha
            if(($this->request->getPost('sts'))==4){
                $success = $model->hapusAbsen($id, $tgl);
            }else{
                $data = array(
                    'sts_absen' => $this->request->getPost('sts'),
                    'ket_absen' => $this->request->getPost('keterangan'),
                );
                //insert data
                $success = $model->editAbsensiswa($data, $id, $tgl);
            }
        }

        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Absensisiswa/koreksiwali/?tgl='.$tgl);
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Absensisiswa/koreksiwali/?tgl='.$tgl);
        }
    }

    public function biweekly()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $m_rombel = new Rombel_model;
        $m_siswarombel = new Siswarombel_model;
        $id_tapel = session()->get('id_tapel');
        $id_rombel = $this->request->getPost('id_rombel');
        
        echo view('func_siswa');

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Laporan Biweekly Siswa',
            'nav' => 'Absensisiswa/biweekly'
        );

        if(empty($this->request->getPost('id_rombel'))){
            $bln = date('m');
            $periode = (date('d') <= 15) ? 1 : 2;
            $thn = date('Y');
            
            if($periode == 1){
                $tgl1 = $thn.'-'.str_pad($bln, 2, '0', STR_PAD_LEFT).'-01';
                $tgl2 = $thn.'-'.str_pad($bln, 2, '0', STR_PAD_LEFT).'-15';
            } else {
                $tgl1 = $thn.'-'.str_pad($bln, 2, '0', STR_PAD_LEFT).'-16';
                $tgl2 = date('Y-m-t', strtotime($thn.'-'.str_pad($bln, 2, '0', STR_PAD_LEFT).'-01'));
            }
            
            $data = array(
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getBulan' => $bln,
                'getPeriode' => $periode,
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'idRombel' => $id_rombel,
                'nmRombel' => ""
            );
        } else {
            $bln = $this->request->getPost('bln');
            $periode = $this->request->getPost('periode');
            $thn = date('Y');
            
            if($periode == 1){
                $tgl1 = $thn.'-'.str_pad($bln, 2, '0', STR_PAD_LEFT).'-01';
                $tgl2 = $thn.'-'.str_pad($bln, 2, '0', STR_PAD_LEFT).'-15';
            } else {
                $tgl1 = $thn.'-'.str_pad($bln, 2, '0', STR_PAD_LEFT).'-16';
                $tgl2 = date('Y-m-t', strtotime($thn.'-'.str_pad($bln, 2, '0', STR_PAD_LEFT).'-01'));
            }
            
            $data = array(
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getBulan' => $bln,
                'getPeriode' => $periode,
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'idRombel' => $id_rombel,
                'nmRombel' => nmrombel($id_rombel)
            );
        }

        echo view('index/sidebar');
        echo view('index/navbar', $datanav);
        echo view('report/biweeklyrombel', $data);
        echo view('index/footer');
    }

    public function bulkUpdateGlobal()
    {
        if (empty(session()->get('logged_in')) || !in_array(session()->get('level'), [1, 4])) {
            return redirect()->to('Cpanel');
        }

        $id_tapel = session()->get('id_tapel');
        $status = $this->request->getPost('status');
        $jam = $this->request->getPost('jam') ?: date('H:i');
        $tgl = $this->request->getPost('tgl') ?: date('Y-m-d');

        if ($tgl > date('Y-m-d')) {
            session()->setFlashdata('error', 'Tanggal koreksi tidak boleh melebihi hari ini.');
            return redirect()->back();
        }

        $db = \Config\Database::connect();
        
        // Get all active students for this tapel
        $students = $db->table('t_siswa_rombel sr')
            ->select('sr.id_siswa')
            ->join('t_siswa s', 's.id_siswa = sr.id_siswa')
            ->where('sr.id_tapel', $id_tapel)
            ->where('s.sts_siswa', 1)
            ->get()->getResultArray();

        if (empty($students)) {
            session()->setFlashdata('error', 'Tidak ada siswa aktif ditemukan di tapel ini.');
            return redirect()->back();
        }

        $db->transStart();

        $studentIds = array_column($students, 'id_siswa');
        if (empty($studentIds)) {
            session()->setFlashdata('error', 'Tidak ada siswa aktif ditemukan di tapel ini.');
            return redirect()->back();
        }

        if (in_array($status, ['Masuk', 'Terlambat', 'Pulang'])) {
            // Category: Kehadiran (t_siswa_hadir)
            $sts_hadir = ($status == 'Pulang') ? 1 : 0;
            
            // Delete conflicting ketidakhadiran (Sakit/Izin/Alpha)
            $db->table('t_siswa_absen')
               ->where('tgl_absen', $tgl)
               ->whereIn('id_siswa', $studentIds)
               ->delete();

            // Get existing kehadiran records
            $existing = $db->table('t_siswa_hadir')
                ->select('id_siswa_hadir, id_siswa')
                ->where('tgl_hadir', $tgl)
                ->where('sts_hadir', $sts_hadir)
                ->whereIn('id_siswa', $studentIds)
                ->get()->getResultArray();
            
            $existingMap = [];
            foreach ($existing as $e) {
                $existingMap[$e['id_siswa']] = $e['id_siswa_hadir'];
            }

            $updateData = [];
            $insertData = [];

            foreach ($studentIds as $id_siswa) {
                if (isset($existingMap[$id_siswa])) {
                    $updateData[] = [
                        'id_siswa_hadir' => $existingMap[$id_siswa],
                        'jam' => $jam
                    ];
                } else {
                    $insertData[] = [
                        'id_siswa' => $id_siswa,
                        'tgl_hadir' => $tgl,
                        'sts_hadir' => $sts_hadir,
                        'jam' => $jam,
                        'id_tapel' => $id_tapel
                    ];
                }
            }

            if (!empty($updateData)) {
                $db->table('t_siswa_hadir')->updateBatch($updateData, 'id_siswa_hadir');
            }
            if (!empty($insertData)) {
                $db->table('t_siswa_hadir')->insertBatch($insertData);
            }

        } else {
            // Category: Ketidakhadiran (t_siswa_absen)
            $sts_absen = ($status == 'Sakit') ? 2 : (($status == 'Izin') ? 3 : 4);
            
            // Delete conflicting kehadiran (Masuk/Pulang)
            $db->table('t_siswa_hadir')
               ->where('tgl_hadir', $tgl)
               ->whereIn('id_siswa', $studentIds)
               ->delete();

            // Get existing ketidakhadiran records
            $existing = $db->table('t_siswa_absen')
                ->select('id_siswa_absen, id_siswa')
                ->where('tgl_absen', $tgl)
                ->whereIn('id_siswa', $studentIds)
                ->get()->getResultArray();

            $existingMap = [];
            foreach ($existing as $e) {
                $existingMap[$e['id_siswa']] = $e['id_siswa_absen'];
            }

            $updateData = [];
            $insertData = [];

            foreach ($studentIds as $id_siswa) {
                if (isset($existingMap[$id_siswa])) {
                    $updateData[] = [
                        'id_siswa_absen' => $existingMap[$id_siswa],
                        'sts_absen' => $sts_absen,
                        'ket_absen' => 'Koreksi Masal Global',
                        'sts_approve' => 1
                    ];
                } else {
                    $insertData[] = [
                        'id_siswa' => $id_siswa,
                        'tgl_absen' => $tgl,
                        'sts_absen' => $sts_absen,
                        'ket_absen' => 'Koreksi Masal Global',
                        'id_tapel' => $id_tapel,
                        'tgl_entri' => date('Y-m-d H:i:s'),
                        'sts_approve' => 1
                    ];
                }
            }

            if (!empty($updateData)) {
                $db->table('t_siswa_absen')->updateBatch($updateData, 'id_siswa_absen');
            }
            if (!empty($insertData)) {
                $db->table('t_siswa_absen')->insertBatch($insertData);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat memproses ' . count($students) . ' data.');
        } else {
            session()->setFlashdata('success', 'Koreksi masal GLOBAL berhasil diproses untuk ' . count($students) . ' siswa.');
        }

        return redirect()->back();
    }
}
