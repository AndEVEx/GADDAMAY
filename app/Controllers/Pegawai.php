<?php

namespace App\Controllers;
use App\Models\Pegawai_model;
use App\Models\Jenisketenagaan_model;

use CodeIgniter\Controller;

class Pegawai extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_jenis = new Jenisketenagaan_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Pegawai',
            'nav' => 'Pegawai'
        );

        $data = array(
            'getPegawai' => $model->getPegawai(),
            'getJenis' => $m_jenis->getJenis()
        );

         
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/pegawai', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        
        $model = new Pegawai_model;
        $file = $this->request->getFile('file');
        $fileName = $file->getRandomName();
        $data = array(
            'nip' => $this->request->getPost('nip'),
            'nama_ptk' => $this->request->getPost('nama'),
            'nama_panggilan' => $this->request->getPost('nm_panggilan'),
            'id_jenis_ptk' => $this->request->getPost('jenis'),
            'no_hp' => $this->request->getPost('hp'),
            'status_ptk' => $this->request->getPost('sts'),
            'kd_jenis_kelamin' => $this->request->getPost('jk'),
            'alamat' => $this->request->getPost('alamat'),
            'nomor_absensi' => $this->request->getPost('no_finger'),
            'password' => password_hash(($this->request->getPost('password')),PASSWORD_DEFAULT),
            'status_absensi' => 1,
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tgl_lahir' => $this->request->getPost('tgl_lahir'),
            'photo' => $fileName,
        );

        //validasi input
        if(!$this->validate([
            "no_finger" => 'required|is_unique[t_ptk.nomor_absensi]'
        ])){
            session()->setFlashdata('error','Ditambahkan, Nomor finger tidak boleh sama');
            return redirect()->to('/Pegawai');
        }

        $success = $model->savePegawai($data);
        $file->move('image/guru/', $fileName);
        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Pegawai');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Pegawai');
        }
        
    }
    public function update()
    {
        $model = new Pegawai_model;
        $id = $this->request->getPost('id');
        if($this->request->getFile('file')->isValid()){
            $file = $this->request->getFile('file');
            $fileName = $file->getRandomName();
            $data = array(
                'nip' => $this->request->getPost('nip'),
                'nama_ptk' => $this->request->getPost('nama'),
                'nama_panggilan' => $this->request->getPost('nm_panggilan'),
                'id_jenis_ptk' => $this->request->getPost('jenis'),
                'no_hp' => $this->request->getPost('hp'),
                'status_ptk' => $this->request->getPost('sts'),
                'kd_jenis_kelamin' => $this->request->getPost('jk'),
                'alamat' => $this->request->getPost('alamat'),
                'nomor_absensi' => $this->request->getPost('no_finger'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tgl_lahir' => $this->request->getPost('tgl_lahir'),
                'photo' => $fileName
            );
            $file->move('image/guru/', $fileName);
        }else{
            $data = array(
                'nip' => $this->request->getPost('nip'),
                'nama_ptk' => $this->request->getPost('nama'),
                'nama_panggilan' => $this->request->getPost('nm_panggilan'),
                'id_jenis_ptk' => $this->request->getPost('jenis'),
                'no_hp' => $this->request->getPost('hp'),
                'status_ptk' => $this->request->getPost('sts'),
                'kd_jenis_kelamin' => $this->request->getPost('jk'),
                'alamat' => $this->request->getPost('alamat'),
                'nomor_absensi' => $this->request->getPost('no_finger'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tgl_lahir' => $this->request->getPost('tgl_lahir')
            );
        }
       
        //update data
        $success = $model->editPegawai($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Pegawai');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Pegawai');
        }
    }
    public function updatepassword()
    {
        $model = new Pegawai_model;
        $id = $this->request->getPost('id');
        $pass1=$this->request->getPost('password1');
        $pass2=$this->request->getPost('password2');

        if($pass1==$pass2)
        {
            $data = array(
                'password' => password_hash($pass1,PASSWORD_DEFAULT)
            );

            //update data
            $success = $model->editPegawai($data, $id);
            if($success){
                session()->setFlashdata('success','Diupdate');
                return redirect()->to('/Pegawai');
            }else{
                session()->setFlashdata('error','Diupdate');
                return redirect()->to('/Pegawai');
            }
        }else
        {
            session()->setFlashdata('error','Diupadete, terdeteksi password tidak sama');
            return redirect()->to('/Pegawai');
        }
    }
    public function hapus()
    {
        $model = new Pegawai_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusPegawai($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Pegawai');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Pegawai');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Pegawai');
        }
    }
}
