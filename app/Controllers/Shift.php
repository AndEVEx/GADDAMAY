<?php

namespace App\Controllers;
use App\Models\Shift_model;
use App\Models\Pegawai_model;
use App\Models\Anggota_model;

use CodeIgniter\Controller;

class Shift extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Shift_model;
        $m_pegawai = new Pegawai_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Shift',
            'nav' => 'Shift'
        );

        $data = array(
            'getShift' => $model->getShift(),
            'getPegawai' => $m_pegawai->getPegawaishift()
        );
 
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/shift', $data);
        echo view('index/footer');
    }
    public function detail()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Shift_model;
        $m_anggota = new Anggota_model;
        $id = $this->request->getPost('id');
        $nm = $this->request->getPost('nm');

        //ambil nama shift
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Detail Anggota Shift',
            'nav' => 'Shift/detail'
        );

        $data = array(
            'getNama' => $nm,
            'getShift' => $model->getShift(),
            'getAnggota' => $m_anggota->getAnggota($id)
        );

        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/detailshift', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        
        $model = new Shift_model;
        $data = array(
            'nm_shift' => $this->request->getPost('nama'),
            'jam_masuk' => $this->request->getPost('jam_masuk'),
            'jam_pulang' => $this->request->getPost('jam_pulang')
        );

        //validasi input
        if(!$this->validate([
            "nama" => 'required|is_unique[r_shift.nm_shift]'
        ])){
            session()->setFlashdata('error','Ditambahkan, Nama Shift tidak boleh sama');
            return redirect()->to('/Shift');
        }

        $success = $model->saveShift($data);
        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Shift');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Shift');
        }
        
    }
    public function addanggota()
    {
        
        $model = new Anggota_model;
        $id_ptk = $this->request->getPost('id_ptk');
        $id_shift = $this->request->getPost('id_shift');
        $jml_data=count($id_ptk);

        for ($i = 0; $i < $jml_data; $i++){
            $data = [
                
                [
                    'id_ptk' => $id_ptk[$i],
                    'id_shift' => $id_shift[$i]
                ],
                
            ];

        //insert data
        
        $success = $model->saveAnggota($data);
        $successhps = $model->hapusAnggota1();
        }

        if($success){
                session()->setFlashdata('success1','Ditambahkan');
                return redirect()->to('/Shift');
        }else{
                session()->setFlashdata('error1','Ditambahkan');
                return redirect()->to('/Shift');
        }
        
    }
    public function update()
    {
        $model = new Shift_model;
        $id = $this->request->getPost('id');
        $data = array(
            'nm_shift' => $this->request->getPost('nama'),
            'jam_masuk' => $this->request->getPost('jam_masuk'),
            'jam_pulang' => $this->request->getPost('jam_pulang')
        );

        //update data
        $success = $model->editShift($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Shift');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Shift');
        }
    }
    public function updateanggota()
    {
        $model = new Anggota_model;
        $id = $this->request->getPost('id');
        $id_shift = $this->request->getPost('id_shift');
        $jml_data=count($id);

        for ($i = 0; $i < $jml_data; $i++){
            $id_anggota = $id[$i];
            $idshift = $id_shift[$i];
            if($idshift==99){
                $success = $model->hapusAnggota($id_anggota);
            }else{
                $data = array(
                    'id_shift' => $id_shift[$i]
                );
                //update data
                $success = $model->editAnggota($data, $id_anggota);
            }
           
        }

        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Shift');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Shift');
        }
    }
    public function hapus()
    {
        $model = new Shift_model;
        $m_anggota = new Anggota_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusShift($id);
            $successhps = $m_anggota->hapusAnggotaall($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Shift');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Shift');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Shift');
        }
    }
}
