<?php

namespace App\Controllers;
use App\Models\Absensiswa_model;

use CodeIgniter\Controller;
date_default_timezone_set('Asia/Jakarta');
class Approveijinsiswa extends Controller
{
    
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensiswa_model;
        $id = session()->get('id_user');

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Approve Pengajuan Izin',
            'nav' => 'Approveijinsiswa'
        );

        $data = array(
            'getAbsen' => $model->getAbsenblmapprove()
        );
 
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/approveijinsiswa', $data);
        echo view('index/footer');
    
    }
    
    public function approve()
    {
        $model = new Absensiswa_model;
        $id = $this->request->getPost('id');
        $tgl = $this->request->getPost('tgl');
       
        $data = array(
            'sts_approve' => $this->request->getPost('status'),
            'tgl_approve' => date('Y-m-d H:i:s')
        );

        //update data
        $success = $model->editAbsen($data, $id, $tgl);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Approveijinsiswa');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Approveijinsiswa');
        }
    }
    public function printijin()
    {

        $id = $this->request->getPost('id');

        $data = array(
            'getId' => $id,
            'getTapel' => session()->get('id_tapel')
        );
       
        echo view('func_siswa');
        echo view('print/ijinsiswa', $data);

        
    }
    public function report()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absensiswa_model;
        $id = session()->get('id_user');

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Pengajuan Izin',
            'nav' => 'Approveijinsiswa/report'
        );

        $data = array(
            'getAbsen' => $model->getAbsenijin()
        );
 
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/approveijinsiswa', $data);
        echo view('index/footer');
    
    }
}
