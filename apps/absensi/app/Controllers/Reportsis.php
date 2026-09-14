<?php

namespace App\Controllers;
use App\Models\Absensisiswa_model;
use App\Models\Absensiswa_model;
use App\Models\Siswa_model;
use App\Models\Rombel_model;
use App\Models\Siswarombel_model;
use App\Models\Pointsiswa_model;
use App\Models\Totalpoint_model;

use CodeIgniter\Controller;

class Reportsis extends Controller
{
    
    public function pertanggal()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $m_absensi = new Absensisiswa_model;
        $id_siswa = session()->get('id_user');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Reportsis/pertanggal'
        );

        if(empty($this->request->getPost('tgl1'))){
            $tgl1 = date('Y-m-d');
            $tgl2 = date('Y-m-d');
            $data = array(
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'getAbsensi' => $m_absensi->getAbsensisiswatgl($id_siswa, $tgl1, $tgl2)
            );
        }else{
            $tgl1 = $this->request->getPost('tgl1');
            $tgl2 = $this->request->getPost('tgl2');

            $data = array(
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'getAbsensi' => $m_absensi->getAbsensisiswatgl($id_siswa, $tgl1, $tgl2)
            );
          
        }

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/pertanggalsiswa', $data);
        echo view('index/footer');
    }
    public function bulanan()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $m_absensi = new Absensisiswa_model;
        $id_tapel = session()->get('id_tapel');
        echo view('func');
        $id_siswa = session()->get('id_user');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Reportsis/bulanan'
        );

        if(empty($this->request->getPost('bln'))){
           
            $data = array(
                'getBulan' => date('m')
            );
        }else{
            $bln = $this->request->getPost('bln');
            
            $data = array(  
                'getBulan' => $bln
            );
            
        }
 
        echo view('index/sidebar');
        echo view('index/navbar',  $datanav);
        echo view('report/bulanansiswa', $data);
        echo view('index/footer');
    }
    public function cetakpertanggalsis()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $m_absensi = new Absensisiswa_model;
        $id_siswa = session()->get('id_user');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Reportsis/pertanggal'
        );

       
            $tgl1 = $this->request->getVar('tgl1');
            $tgl2 = $this->request->getVar('tgl2');

            $data = array(
                'getTanggal1' => $tgl1,
                'getTanggal2' => $tgl2,
                'getAbsensi' => $m_absensi->getAbsensisiswatgl($id_siswa, $tgl1, $tgl2)
            );
        
        echo view('func');
        echo view('print/pertanggalsiswa', $data);

    }
    public function cetakbulanansis()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $m_absensi = new Absensisiswa_model;
        $id_tapel = session()->get('id_tapel');
        echo view('func');
        $id_siswa = session()->get('id_user');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Reportsis/bulanan'
        );

        $bln = $this->request->getVar('bln');
            
        $data = array(  
            'getBulan' => $bln
        );

        echo view('print/bulanansiswa', $data);
    }
    
    
    public function point()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pointsiswa_model;
       
        $id_tapel = session()->get('id_tapel');
        echo view('func');
        $id_siswa = session()->get('id_user');

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Point Siswa',
            'nav' => 'Reportsis/Point'
        );

        if(empty($this->request->getPost('bln'))){
            $bln = date('m');
        }else{
            $bln = $this->request->getPost('bln');
        }
        $data = array(
            'getPoint' => $model->getPointsiswabln($id_siswa,$bln),
            'getBulan' => $bln
        );
        
        echo view('index/sidebar');

        echo view('index/navbar',  $datanav);
        echo view('report/pointsiswa', $data);
        echo view('index/footer');
    }
}
