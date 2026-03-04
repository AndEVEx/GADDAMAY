<?php

namespace App\Controllers;
use App\Models\Jenisketenagaan_model;
use App\Models\Pegawai_model;
use App\Models\Shift_model;
use App\Models\Libur_model;
use CodeIgniter\Controller;

class Libur extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Libur_model;
        $m_pegawai = new Pegawai_model;
        $m_shift = new Shift_model;
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Libur Perindividu',
            'nav' => 'Libur'
        );

        $data = array(
            'getLibur' => $model->getLibur(),
            'getPegawai' => $m_pegawai->getPegawai(),
            'getShift' => $m_shift->getShift()
        );
 
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('setting/libur', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Libur_model;
        $data = array(
            'id_ptk' => $this->request->getPost('id_ptk'),
            'Senin' => $this->request->getPost('senin'),
            'Selasa' => $this->request->getPost('selasa'),
            'Rabu' => $this->request->getPost('rabu'),
            'Kamis' => $this->request->getPost('kamis'),
            'Jumat' => $this->request->getPost('jumat'),
            'Sabtu' => $this->request->getPost('sabtu'),
            'Minggu' => $this->request->getPost('minggu'),
            'tgl_entri' => date('Y-m-d')
        );

        //validasi input
        if(!$this->validate([
            "id_ptk" => 'required|is_unique[libur.id_ptk]'
        ])){
            session()->setFlashdata('error','Ditambahkan, Nama pegawai tidak boleh sama');
            return redirect()->to('/Libur');
        }
       
        //insert data
        $success = $model->saveLibur($data);
     
        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Libur');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Libur');
        }
        
    }
    public function update()
    {
        $model = new Libur_model;
        $id = $this->request->getPost('id');
        $data = array(
            'Senin' => $this->request->getPost('senin'),
            'Selasa' => $this->request->getPost('selasa'),
            'Rabu' => $this->request->getPost('rabu'),
            'Kamis' => $this->request->getPost('kamis'),
            'Jumat' => $this->request->getPost('jumat'),
            'Sabtu' => $this->request->getPost('sabtu'),
            'Minggu' => $this->request->getPost('minggu')
        );

        //update data
        $success = $model->editLibur($data, $id);
        if($success){
            session()->setFlashdata('success1','Diupdate');
            return redirect()->to('/Libur');
        }else{
            session()->setFlashdata('error1','Diupdate');
            return redirect()->to('/Libur');
        }
    }
    public function hapus()
    {
        $model = new Libur_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusLibur($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Libur');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Libur');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Libur');
        }
    }
}
