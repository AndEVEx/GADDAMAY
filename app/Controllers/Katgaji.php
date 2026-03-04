<?php

namespace App\Controllers;
use App\Models\Katgaji_model;
use App\Models\Jenisketenagaan_model;

use CodeIgniter\Controller;

class Katgaji extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Katgaji_model;
        $m_jenis = new Jenisketenagaan_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Kategori Penggajian',
            'nav' => 'Katgaji'
        );

        $data = array(
            'getJenis' => $m_jenis->getJenis(),
            'getKetgaji' => $model->getKatgaji()
        );

        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/katgaji', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Katgaji_model;
        $data = array(
            'nm_kat_gaji' => $this->request->getPost('nama'),
            'sts_kat_gaji' => 1
        );

        //validasi input
        if(!$this->validate([
            "nama" => 'required|is_unique[r_kat_gaji.nm_kat_gaji]'
        ])){
            session()->setFlashdata('error','Ditambahkan, Nama Kategori Gaji tidak boleh sama');
            return redirect()->to('/Katgaji');
        }

        $success = $model->saveKatgaji($data);
        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Katgaji');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Katgaji');
        }
        
    }
    public function update()
    {
        $model = new Katgaji_model;
        $id = $this->request->getPost('id');
        $data = array(
            'nm_kat_gaji' => $this->request->getPost('nama'),
            'sts_kat_gaji' => $this->request->getPost('sts')
        );

        //update data
        $success = $model->editKatgaji($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Katgaji');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Katgaji');
        }
    }
    public function hapus()
    {
        $model = new Katgaji_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusKatgaji($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Katgaji');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Katgaji');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Katgaji');
        }
    }
}
