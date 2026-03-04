<?php

namespace App\Controllers;
use App\Models\Absen_model;

use CodeIgniter\Controller;
date_default_timezone_set('Asia/Jakarta');
class Ajukanizin extends Controller
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
            'title' => 'Pengajuan Izin',
            'nav' => 'Ajukanizin'
        );

        $data = array(
            'getAbsen' => $model->getAbsenpegawai($id)
        );

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/ajukan', $data);
        echo view('index/footer');
   
    }
    public function add()
    {
        
        $model = new Absen_model;
        $file = $this->request->getFile('file');
        $fileName = $file->getRandomName();
        $file->move('ijin/', $fileName);
        $data = array(
            'NO_INDUK' => session()->get('username'),
            'ID_PEGAWAI' => session()->get('id_user'),
            'TANGGAL_ABSEN' => $this->request->getPost('tgl'),
            'STATUS' => $this->request->getPost('status'),
            'KETERANGAN' => $this->request->getPost('keterangan'),
            'STS' => 0,
            'FILE' => $fileName,
            'TANGGAL_AKSES' => date('Y-m-d H:i:s')
        );
      
        $success = $model->saveAbsen($data);
       
      
        if($success){
            session()->setFlashdata('success','Ditambahkan');
            return redirect()->to('/Ajukanizin');
        }else{
            session()->setFlashdata('error','Ditambahkan');
            return redirect()->to('/Ajukanizin');
        }
        
    }
}
