<?php

namespace App\Controllers;
use App\Models\Pointsiswa_model;
use App\Models\Totalpoint_model;
use App\Models\Siswa_model;

use CodeIgniter\Controller;

class Pointsiswa extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Totalpoint_model;
        $m_siswa = new Siswa_model;
        $id_tapel = session()->get('id_tapel');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => '50 Siswa Point Terbaik',
            'nav' => 'Point'
        );

        if(empty($this->request->getPost('bln'))){
            $bln = date('m');
        }else{
            $bln = $this->request->getPost('bln');
        }
        $data = array(
            'getPointSiswa' => $model->getTotalpoint($bln,  $id_tapel),
            'getBulan' => $bln
        );
        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/pointsis', $data);
        echo view('index/footer');
    }
    
}
