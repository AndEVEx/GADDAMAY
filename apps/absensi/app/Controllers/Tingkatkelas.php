<?php

namespace App\Controllers;
use App\Models\Tingkatkelas_model;

use CodeIgniter\Controller;

class Tingkatkelas extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Tingkatkelas_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Tingkat Kelas',
            'nav' => 'Tingkatkelas'
        );

        $data = array(
            'getTingkat' => $model->getTingkat()
        );

        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/tingkatkelas', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Tingkatkelas_model;
        $data = array(
            'nm_tingkat_kelas' => $this->request->getPost('nama')
        );

        //validasi input
        if(!$this->validate([
            "nama" => 'required|is_unique[r_tingkat_kelas.nm_tingkat_kelas]'
        ])){
            session()->setFlashdata('error','Ditambahkan, Nama Tingkat Kelas tidak boleh sama');
            return redirect()->to('/Tingkatkelas');
        }

        $success = $model->saveTingkat($data);
        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Tingkatkelas');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Tingkatkelas');
        }
        
    }
    public function update()
    {
        $model = new Tingkatkelas_model;
        $id = $this->request->getPost('id');
        $data = array(
            'nm_tingkat_kelas' => $this->request->getPost('nama')
        );

        //update data
        $success = $model->editTingkat($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Tingkatkelas');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Tingkatkelas');
        }
    }
    public function hapus()
    {
        $model = new Tingkatkelas_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusTingkat($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Tingkatkelas');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Tingkatkelas');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Tingkatkelas');
        }
    }
}
