<?php

namespace App\Controllers;
use App\Models\Tapel_model;

use CodeIgniter\Controller;

class Tapel extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Tapel_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Tahun Pelajaran',
            'nav' => 'Tapel'
        );

        $data = array(
            'getTapel' => $model->getTapel()
        );

        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/tapel', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Tapel_model;
        $data = array(
            'nm_tapel' => $this->request->getPost('nama'),
            'sts_aktif' => $this->request->getPost('sts')
        );

        //validasi input
        if(!$this->validate([
            "nama" => 'required|is_unique[r_tapel.nm_tapel]'
        ])){
            session()->setFlashdata('error','Ditambahkan, Nama Tapel tidak boleh sama');
            return redirect()->to('/Tapel');
        }

        $success = $model->saveTapel($data);
        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Tapel');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Tapel');
        }
        
    }
    public function update()
    {
        $model = new Tapel_model;
        $id = $this->request->getPost('id');
        $data = array(
            'nm_tapel' => $this->request->getPost('nama'),
            'sts_aktif' => $this->request->getPost('sts')
        );

        //update data
        $success = $model->editTapel($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Tapel');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Tapel');
        }
    }
    public function hapus()
    {
        $model = new Tapel_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusTapel($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Tapel');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Tapel');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Tapel');
        }
    }
}
