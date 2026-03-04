<?php

namespace App\Controllers;
use App\Models\Pegawai_model;

use CodeIgniter\Controller;

class Profile extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $id = session()->get('id_user');
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Profile',
            'nav1' => 'Profile'
        );

        $data = array(
            'getPegawai' => $model->getPegawaidetail($id)
        );
        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/profile', $data);
        echo view('index/footer');
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
                'nik' => $this->request->getPost('nik'),
                'nama_ptk' => $this->request->getPost('nama'),
                'no_hp' => $this->request->getPost('hp'),
                'alamat' => $this->request->getPost('alamat'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tgl_lahir' => $this->request->getPost('tgl_lahir'),
                'photo' => $fileName
            );
            $file->move('image/guru/', $fileName);

        }else{
            $data = array(
                'nip' => $this->request->getPost('nip'),
                'nik' => $this->request->getPost('nik'),
                'nama_ptk' => $this->request->getPost('nama'),
                'no_hp' => $this->request->getPost('hp'),
                'alamat' => $this->request->getPost('alamat'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tgl_lahir' => $this->request->getPost('tgl_lahir')
            );
        }
        
       
        //update data
        $success = $model->editPegawai($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Profile');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Profile');
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
                session()->setFlashdata('successpass','Diupdate');
                return redirect()->to('/Profile');
            }else{
                session()->setFlashdata('errorpass','Diupdate');
                return redirect()->to('/Profile');
            }
        }else
        {
            session()->setFlashdata('errorpass','Diupadete, terdeteksi password tidak sama');
            return redirect()->to('/Profile');
        }
    }
}
