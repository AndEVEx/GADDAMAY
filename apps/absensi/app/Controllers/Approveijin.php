<?php

namespace App\Controllers;
use App\Models\Absen_model;

use CodeIgniter\Controller;
date_default_timezone_set('Asia/Jakarta');
class Approveijin extends Controller
{
    
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absen_model;
        $id = session()->get('id_user');

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Approve Pengajuan Izin',
            'nav' => 'Approveijin'
        );

        $data = array(
            'getAbsen' => $model->getAbsenblmapprove()
        );

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/approveijin', $data);
        echo view('index/footer');
   
    }
    
    public function approve()
    {
        $model = new Absen_model;
        $id = $this->request->getPost('id');
        $tgl = $this->request->getPost('tgl');
       
        $data = array(
            'STS' => $this->request->getPost('status'),
            'TANGGAL_APPROVE' => date('Y-m-d H:i:s')
        );

        //update data
        $success = $model->editAbsen($data, $id, $tgl);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Approveijin');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Approveijin');
        }
    }
    public function printijin()
    {

        $id = $this->request->getPost('id');
        $tgl = $this->request->getPost('tgl');
        $data = array(
            'getId' => $id,
            'getTanggal' => $tgl
        );
       
        echo view('func');
        echo view('print/ijinguru', $data);

    }
    public function report()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absen_model;
        $id = session()->get('id_user');

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Pengajuan Izin Guru/Karyawan',
            'nav' => 'Approveijin/report'
        );

        $data = array(
            'getAbsen' => $model->getAbsenijin()
        );

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/approveijin', $data);
        echo view('index/footer');
   
    }
}
