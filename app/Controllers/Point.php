<?php

namespace App\Controllers;
use App\Models\Point_model;
use App\Models\Pegawai_model;

use CodeIgniter\Controller;

class Point extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Point_model;
        $m_pegawai = new Pegawai_model;
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Point',
            'nav' => 'Point'
        );

        if(empty($this->request->getPost('bln'))){
            $bln = date('m');
        }else{
            $bln = $this->request->getPost('bln');
        }
        $data = array(
            'getPointkaryawan' => $model->getPointkaryawan(),
            'getKaryawan' => $m_pegawai->getPegawaikary(),
            'getBulan' => $bln
        );
          
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/point', $data);
        echo view('index/footer');
    }
   
}
