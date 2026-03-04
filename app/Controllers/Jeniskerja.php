<?php

namespace App\Controllers;
use App\Models\Jenisketenagaan_model;

use CodeIgniter\Controller;

class Jeniskerja extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $m_jenis = new Jenisketenagaan_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Jenis Kerja',
            'nav' => 'Jeniskerja'
        );

        $data = array(
            'getJenis' => $m_jenis->getJenis()
        );

        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/jeniskerja', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Jenisketenagaan_model;
        $data = array(
            'nama_jenis_ptk' => $this->request->getPost('nama')
        );

        //validasi input
        if(!$this->validate([
            "nama" => 'required|is_unique[r_jenis_ptk.nama_jenis_ptk]'
        ])){
            session()->setFlashdata('error','Ditambahkan, Nama Jenis Kerja tidak boleh sama');
            return redirect()->to('/Jeniskerja');
        }

        $success = $model->saveJenis($data);
        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Jeniskerja');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Jeniskerja');
        }
        
    }
    public function update()
    {
        $model = new Jenisketenagaan_model;
        $id = $this->request->getPost('id');
        $data = array(
            'nama_jenis_ptk' => $this->request->getPost('nama')
        );

        //update data
        $success = $model->editJenis($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Jeniskerja');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Jeniskerja');
        }
    }
    public function hapus()
    {
        $model = new Jenisketenagaan_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusJenis($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Jeniskerja');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Jeniskerja');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Jeniskerja');
        }
    }
}
