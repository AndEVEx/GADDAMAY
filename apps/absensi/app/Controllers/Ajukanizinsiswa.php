<?php

namespace App\Controllers;
use App\Models\Absensiswa_model;

use CodeIgniter\Controller;
date_default_timezone_set('Asia/Jakarta');
class Ajukanizinsiswa extends Controller
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
            'title' => 'Pengajuan Izin',
            'nav' => 'Ajukanizin'
        );

        $data = array(
            'getAbsen' => $model->getAbsensiswa($id)
        );

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/ajukansiswa', $data);
        echo view('index/footer');
   
    }
    public function add()
    {
        
        $model = new Absensiswa_model;
        $file = $this->request->getFile('file');
        $fileName = $file->getRandomName();
       

        $data = array(
            'id_siswa' => session()->get('id_user'),
            'tgl_absen' => $this->request->getPost('tgl'),
            'sts_absen' => $this->request->getPost('status'),
            'ket_absen' => $this->request->getPost('keterangan'),
            'id_tapel' => session()->get('id_tapel'),
            'tgl_entri' => date('Y-m-d H:i:s'),
            'sts_approve' => 0,
            'file' => $fileName,
            'tgl_approve' => date('Y-m-d H:i:s')
        );
       
        $success = $model->saveAbsen($data);
        $file->move('ijin/', $fileName);
        
        if($success){
            session()->setFlashdata('success','Ditambahkan');
            return redirect()->to('/Ajukanizinsiswa');
        }else{
            session()->setFlashdata('error','Ditambahkan');
            return redirect()->to('/Ajukanizinsiswa');
        }
        
    }
}
