<?php

namespace App\Controllers;
use App\Models\Absensisiswa_model;
use App\Models\Absensiswa_model;
use App\Models\Siswa_model;
use App\Models\Rombel_model;
use App\Models\Siswarombel_model;
use App\Models\Pointsiswa_model;
use App\Models\Totalpointsiswa_model;

use CodeIgniter\Controller;

class Reportwal extends Controller
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
        echo view('func_siswa');
        $id_rombel = rombelwalikelas_or_bk(session()->get('id_user'),$id_tapel);

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Reportwal'
        );

            if(empty($this->request->getPost('tgl'))){
                $tgl = date('Y-m-d');
            }else{
                $tgl = $this->request->getPost('tgl');
            }
            
            $data = array(
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getTanggal' => $tgl,
                'idRombel' => $id_rombel,
                'nmRombel' => nmrombel($id_rombel)
            );
        

        echo view('index/sidebar');
        
        echo view('index/navbar',  $datanav);
        echo view('report/harianrombel', $data);
        echo view('index/footer');
    }

    public function cetakharian()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $m_rombel = new Rombel_model;
        $m_siswarombel = new Siswarombel_model;
        $id_tapel = session()->get('id_tapel');
        echo view('func_siswa');
        $id_rombel = rombelwalikelas_or_bk(session()->get('id_user'),$id_tapel);
        $tgl = $this->request->getVar('tgl') ?? date('Y-m-d');
        
        $data = array(
            'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
            'getRombel' => $m_rombel->getRombel($id_tapel),
            'getTanggal' => $tgl,
            'idRombel' => $id_rombel,
            'nmRombel' => nmrombel($id_rombel),
            'title' => 'Print Info Absensi',
        );

        echo view('print/harianrombel', $data);
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
        echo view('func_siswa');
        $id_rombel = rombelwalikelas_or_bk(session()->get('id_user'),$id_tapel);
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Reportwal/pertanggal'
        );
        
        if(empty($this->request->getPost('tgl1'))){
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
        echo view('report/pertanggalrombel', $data);
        echo view('index/footer');
    }
    public function printpertanggal()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswa_model;
        $m_rombel = new Rombel_model;
        $m_siswarombel = new Siswarombel_model;
        $id_tapel = session()->get('id_tapel');
        echo view('func_siswa');
        $id_rombel = rombelwalikelas_or_bk(session()->get('id_user'),$id_tapel);
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Reportwal/pertanggal'
        );
        
            $tgl1 = $this->request->getVar('tgl1');
            $tgl2 = $this->request->getVar('tgl2');
           
            $data = array(
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'idRombel' => $id_rombel,
                'nmRombel' => nmrombel($id_rombel)
            );

       
        echo view('print/pertanggalrombel', $data);
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
        echo view('func_siswa');
        $id_rombel = rombelwalikelas_or_bk(session()->get('id_user'), $id_tapel);
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Reportwal/bulanan'
        );

        if(empty($this->request->getPost('bln'))){
            $bln = date('m');
            $data = array(
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getBulan' => $bln,
                'idRombel' => $id_rombel,
                'nmRombel' => "",
                'title' => 'Info Absensi',
                'nav' => 'Reportwal/bulanan'
            );
        }else{
            $bln = $this->request->getPost('bln');
            
            $data = array(
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getBulan' => $bln,
                'idRombel' => $id_rombel,
                'nmRombel' => nmrombel($id_rombel),
                'title' => 'Info Absensi',
                'nav' => 'Reportwal/bulanan'
            );
            
        }
 
        echo view('index/sidebar');
        echo view('index/navbar',  $datanav);
        echo view('report/bulananrombel', $data);
        echo view('index/footer');
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
        echo view('func_siswa');
        $id_rombel = rombelwalikelas_or_bk(session()->get('id_user'), $id_tapel);
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Reportwal/bulanan'
        );
        
        $bln = $this->request->getVar('bln');
            
            $data = array(
                'getRombel' => $m_rombel->getRombel($id_tapel),
                'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
                'getBulan' => $bln,
                'idRombel' => $id_rombel,
                'nmRombel' => nmrombel($id_rombel),
                'title' => 'Info Absensi',
                'nav' => 'Reportwal/bulanan'
            );

        echo view('print/bulananrombel', $data);
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
        echo view('func_siswa');
        $id_rombel = rombelwalikelas_or_bk(session()->get('id_user'),$id_tapel);
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi Persiswa',
            'nav' => 'Reportwal/persiswa'
        );

        if(empty($this->request->getPost('id_siswa'))){
            $data = array(
                'getAbsensi' => $model->getAbsensihari($tgl),
                'getSiswa' => $m_siswa->getSiswarombel($id_rombel),
                'getNama' => "",
                'nmRombel' => "",
                'idSiswa' => "",
                'title' => 'Info Absensi Persiswa',
                'nav' => 'Reportwal/persiswa'
            );
        }else{
            $id_siswa = $this->request->getPost('id_siswa');
            
            // Jika user klik Lihat Data tapi tidak memilih siswa (value "Pilih")
            if ($id_siswa == 'Pilih' || empty($id_siswa)) {
                return redirect()->to('/Reportwal/persiswa');
            }

            //ambil data siswq
            $query = $db->query("SELECT no_induk,nm_siswa,nm_rombel FROM t_siswa 
            JOIN t_siswa_rombel ON t_siswa_rombel.id_siswa = t_siswa.id_siswa
            JOIN t_rombel ON t_rombel.id_rombel = t_siswa_rombel.id_rombel
            where t_siswa.id_siswa='$id_siswa' and t_siswa_rombel.id_tapel='$id_tapel'");
            $row = $query->getRow();

            $data = array(
                'getAbsensi' => $model->getAbsensisiswa($id_siswa, $tgl),
                'getSiswa' => $m_siswa->getSiswarombel($id_rombel),
                'getNama' => $row ? $row->nm_siswa : '',
                'nmRombel' => $row ? $row->nm_rombel : '',
                'idSiswa' => $id_siswa,
                'title' => 'Info Absensi Persiswa',
                'nav' => 'Reportwal/persiswa'
            );
        }
        
        echo view('index/sidebar');
         
        echo view('index/navbar',  $datanav);
        echo view('report/persiswarombel', $data);
        echo view('index/footer');
    }
    
    public function point()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Totalpoint_model;
        $m_siswa = new Siswa_model;
        $id_tapel = session()->get('id_tapel');
        echo view('func');
        $id_rombel = rombelwalikelas(session()->get('id_user'));

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Point Siswa',
            'nav' => 'Reportwal/Point'
        );

        if(empty($this->request->getPost('bln'))){
            $bln = date('m');
        }else{
            $bln = $this->request->getPost('bln');
        }
        $data = array(
            'getPointSiswa' => $model->getTotalpointrombel($bln,  $id_tapel, $id_rombel),
            'getBulan' => $bln
        );
        
        echo view('index/sidebar');

        echo view('index/navbar',  $datanav);
        echo view('report/pointrombel', $data);
        echo view('index/footer');
    }
}
