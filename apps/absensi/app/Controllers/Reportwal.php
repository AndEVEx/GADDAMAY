<?php

namespace App\Controllers;
use App\Models\Absensisiswa_model;
use App\Models\Absensiswa_model;
use App\Models\Siswa_model;
use App\Models\Rombel_model;
use App\Models\Siswarombel_model;
use App\Models\Pointsiswa_model;
use App\Models\Totalpointsiswa_model;
use App\Models\Totalpoint_model;

use CodeIgniter\Controller;

class Reportwal extends Controller
{
    private function getMyRombels()
    {
        $id_user = session()->get('id_user');
        $id_tapel = session()->get('id_tapel');
        $db = \Config\Database::connect();

        $waliRombels = $db->table('t_rombel')
            ->where('id_walikelas', $id_user)
            ->where('id_tapel', $id_tapel)
            ->get()->getResultArray();

        $bkRombels = $db->table('t_rombel')
            ->where('id_guru_bk', $id_user)
            ->where('id_tapel', $id_tapel)
            ->get()->getResultArray();

        $allRombels = [];
        foreach ($waliRombels as $r) {
            $allRombels[$r['id_rombel']] = $r;
        }
        foreach ($bkRombels as $r) {
            $allRombels[$r['id_rombel']] = $r;
        }

        return $allRombels;
    }

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

        $myRombels = $this->getMyRombels();
        $id_rombel = $this->request->getPost('id_rombel');
        if (empty($id_rombel)) {
            $id_rombel = $this->request->getGet('id_rombel');
        }
        if (empty($id_rombel)) {
            $id_rombel = !empty($myRombels) ? array_keys($myRombels)[0] : 0;
        }

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
                'getRombel' => $myRombels,
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

        $myRombels = $this->getMyRombels();
        $id_rombel = $this->request->getVar('id_rombel');
        if (empty($id_rombel)) {
            $id_rombel = $this->request->getVar('id');
        }
        if (empty($id_rombel)) {
            $id_rombel = !empty($myRombels) ? array_keys($myRombels)[0] : 0;
        }
        
        $tgl = $this->request->getVar('tgl') ?? date('Y-m-d');
        
        $data = array(
            'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
            'getRombel' => $myRombels,
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

        $myRombels = $this->getMyRombels();
        $id_rombel = $this->request->getPost('id_rombel');
        if (empty($id_rombel)) {
            $id_rombel = !empty($myRombels) ? array_keys($myRombels)[0] : 0;
        }

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Reportwal/pertanggal'
        );
        
        $tgl1 = $this->request->getPost('tgl1') ?? date('Y-m-d');
        $tgl2 = $this->request->getPost('tgl2') ?? date('Y-m-d');
       
        $data = array(
            'getRombel' => $myRombels,
            'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
            'getTanggal1' => $tgl1,
            'getTanggal2' => $tgl2,
            'idRombel' => $id_rombel,
            'nmRombel' => nmrombel($id_rombel)
        );

        echo view('index/sidebar');
        
        echo view('index/navbar',  $datanav);
        echo view('report/pertanggalsis', $data);
        echo view('index/footer');
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
        echo view('func_siswa');

        $myRombels = $this->getMyRombels();
        $id_rombel = $this->request->getVar('id_rombel');
        if (empty($id_rombel)) {
            $id_rombel = $this->request->getVar('idrombel');
        }
        if (empty($id_rombel)) {
            $id_rombel = !empty($myRombels) ? array_keys($myRombels)[0] : 0;
        }

        $tgl1 = $this->request->getVar('tgl1');
        $tgl2 = $this->request->getVar('tgl2');
           
        $data = array(
            'getRombel' => $myRombels,
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

        $myRombels = $this->getMyRombels();
        $id_rombel = $this->request->getPost('id_rombel');
        if (empty($id_rombel)) {
            $id_rombel = !empty($myRombels) ? array_keys($myRombels)[0] : 0;
        }

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi',
            'nav' => 'Reportwal/bulanan'
        );

        $bln = $this->request->getPost('bln') ?? date('m');
        
        $data = array(
            'getRombel' => $myRombels,
            'getSiswa' => $m_siswarombel->getSiswarombel($id_rombel),
            'getBulan' => $bln,
            'idRombel' => $id_rombel,
            'nmRombel' => nmrombel($id_rombel),
            'title' => 'Info Absensi',
            'nav' => 'Reportwal/bulanan'
        );
 
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

        $myRombels = $this->getMyRombels();
        $id_rombel = $this->request->getVar('id_rombel');
        if (empty($id_rombel)) {
            $id_rombel = $this->request->getVar('idrombel');
        }
        if (empty($id_rombel)) {
            $id_rombel = !empty($myRombels) ? array_keys($myRombels)[0] : 0;
        }
        
        $bln = $this->request->getVar('bln');
            
        $data = array(
            'getRombel' => $myRombels,
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

        $myRombels = $this->getMyRombels();
        $id_rombel = $this->request->getPost('id_rombel');
        if (empty($id_rombel)) {
            $id_rombel = !empty($myRombels) ? array_keys($myRombels)[0] : 0;
        }

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absensi Persiswa',
            'nav' => 'Reportwal/persiswa'
        );

        $id_siswa = $this->request->getPost('id_siswa');

        if(empty($id_siswa) || $id_siswa == 'Pilih'){
            $data = array(
                'getAbsensi' => $model->getAbsensihari($tgl),
                'getSiswa' => $m_siswa->getSiswarombel($id_rombel),
                'getNama' => "",
                'nmRombel' => "",
                'idSiswa' => "",
                'getRombel' => $myRombels,
                'idRombel' => $id_rombel,
                'title' => 'Info Absensi Persiswa',
                'nav' => 'Reportwal/persiswa'
            );
        }else{
            
            //ambil data siswa
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
                'getRombel' => $myRombels,
                'idRombel' => $id_rombel,
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
        
        $myRombels = $this->getMyRombels();
        $id_rombel = !empty($myRombels) ? array_keys($myRombels)[0] : 0;

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
