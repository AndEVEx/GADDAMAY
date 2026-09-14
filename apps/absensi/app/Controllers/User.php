<?php

namespace App\Controllers;
use App\Models\User_model;

use CodeIgniter\Controller;

class User extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new User_model;
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Setting User',
            'nav' => 'User'
        );

        $data = array(
            'getUser' => $model->getUser()
        );

        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('setting/user', $data);
        echo view('index/footer');
    }
    public function add()
    {
        
        $model = new User_model;
        $pass1=$this->request->getPost('password1');
        $pass2=$this->request->getPost('password2');
        $file = $this->request->getFile('file');
        $fileName = $file->getRandomName();
        $data = array(
            'username' => $this->request->getPost('username'),
            'password' => password_hash($pass1,PASSWORD_DEFAULT),
            'nama' => $this->request->getPost('nama'),
            'level' => $this->request->getPost('level'),
            'foto' => $fileName
        );

        //validasi input
        if(!$this->validate([
            "username" => 'required|is_unique[t_user.username]'
        ])){
            session()->setFlashdata('validation','Username tidak boleh sama');
            return redirect()->to('/User');
        }
        

        if($pass1==$pass2){
            //insert data
            $success = $model->saveUser($data);
            $file->move('image/', $fileName);
            if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/User');
            }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/User');
            }
        }else{
            session()->setFlashdata('error','Ditambahkan, password terdeteksi tidak sama');
            return redirect()->to('/User');
        }
        
        
    }
    public function update()
    {
        $model = new User_model;
        $id = $this->request->getPost('id');
        if($this->request->getFile('file')->isValid()){
            $file = $this->request->getFile('file');
            $fileName = $file->getRandomName();
            $data = array(
                'nama' => $this->request->getPost('nama'),
                'username' => $this->request->getPost('username'),
                'level' => $this->request->getPost('level'),
                'foto' => $fileName
            );
            $file->move('image/', $fileName);
        }else{
            $data = array(
                'nama' => $this->request->getPost('nama'),
                'username' => $this->request->getPost('username'),
                'level' => $this->request->getPost('level')
            );
        }

        //update data
        $success = $model->editUser($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/User');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/User');
        }
    }
    public function updatepassword()
    {
        $model = new User_model;
        $id = $this->request->getPost('id');
        $pass1=$this->request->getPost('password1');
        $pass2=$this->request->getPost('password2');

        if($pass1==$pass2)
        {
            $data = array(
                'password' => password_hash($pass1,PASSWORD_DEFAULT)
            );

            //update data
            $success = $model->editUser($data, $id);
            if($success){
                session()->setFlashdata('success','Diupdate');
                return redirect()->to('/User');
            }else{
                session()->setFlashdata('error','Diupdate');
                return redirect()->to('/User');
            }
        }else
        {
            session()->setFlashdata('error','Diupadete, terdeteksi password tidak sama');
            return redirect()->to('/User');
        }
    }

    public function hapus()
    {
        $model = new User_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusUser($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/User');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/User');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/User');
        }
    }
}
