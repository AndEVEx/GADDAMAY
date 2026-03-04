<?php

namespace App\Controllers;
use App\Models\Jadwalkhusus_model;
use App\Models\Pegawai_model;
use App\Models\Shift_model;

use CodeIgniter\Controller;

class Jadwalkhusus extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Jadwalkhusus_model;
        $m_pegawai = new Pegawai_model;
        $m_shift = new Shift_model;
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Jadwal khusus Per Guru',
            'nav' => 'Jadwalkhusus'
        );

        $data = array(
            'getJadwalkhusus' => $model->getJadwalkhusus(),
            'getPegawai' => $m_pegawai->getPegawai(),
            'getShift' => $m_shift->getShift()
        );
 
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('setting/jadwalkhusus', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Jadwalkhusus_model;
        $data = array(
            'id_ptk' => $this->request->getPost('id_ptk'),
            'Senin' => $this->request->getPost('id_shift1'),
            'Selasa' => $this->request->getPost('id_shift2'),
            'Rabu' => $this->request->getPost('id_shift3'),
            'Kamis' => $this->request->getPost('id_shift4'),
            'Jumat' => $this->request->getPost('id_shift5'),
            'Sabtu' => $this->request->getPost('id_shift6'),
            'Minggu' => $this->request->getPost('id_shift7'),
            'tgl_entri' => date('Y-m-d')
        );

        //validasi input
        if(!$this->validate([
            "id_ptk" => 'required|is_unique[jadwal_khusus.id_ptk]'
        ])){
            session()->setFlashdata('error','Ditambahkan, Nama pegawai tidak boleh sama');
            return redirect()->to('/Jadwalkhusus');
        }
       
        //insert data
        $success = $model->saveJadwalkhusus($data);
     
        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Jadwalkhusus');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Jadwalkhusus');
        }
        
    }
    public function update()
    {
        $model = new Jadwalkhusus_model;
        $id = $this->request->getPost('id');
        $data = array(
            'Senin' => $this->request->getPost('id_shift1'),
            'Selasa' => $this->request->getPost('id_shift2'),
            'Rabu' => $this->request->getPost('id_shift3'),
            'Kamis' => $this->request->getPost('id_shift4'),
            'Jumat' => $this->request->getPost('id_shift5'),
            'Sabtu' => $this->request->getPost('id_shift6'),
            'Minggu' => $this->request->getPost('id_shift7'),
            'tgl_entri' => date('Y-m-d')
        );

        //update data
        $success = $model->editJadwalkhusus($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Jadwalkhusus');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Jadwalkhusus');
        }
    }
    public function hapus()
    {
        $model = new Jadwalkhusus_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusJadwalkhusus($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Jadwalkhusus');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Jadwalkhusus');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Jadwalkhusus');
        }
    }
}
