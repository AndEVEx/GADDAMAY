<?php

namespace App\Controllers;
use App\Models\Setting_model;
use App\Models\Hari_model;

use CodeIgniter\Controller;

class Setting extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Setting_model;
        $m_hari = new Hari_model;
        $db = \Config\Database::connect();
        $builder = $db->table('t_setting_aplikasi');
        $query =  $builder->get();
        $aplikasi = $query->getRow();
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Setting Aplikasi',
            'nav' => 'Setting'
        );

        $data = array(
            'getSetting' => $model->getSetting(),
            'getAplikasi' => $aplikasi->nm_aplikasi,
            'getNamasekolah' => $aplikasi->nm_sekolah,
            'getAlamat' => $aplikasi->alamat,
            'getNamakepsek' => $aplikasi->nm_kepsek,
            'getHari' => $m_hari->getHari()
        );

        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('setting/setting', $data);
        echo view('index/footer');
    }
   
    public function update()
    {
        $model = new Setting_model;
        $id = 1;
        if($this->request->getFile('file')->isValid()){
            $file = $this->request->getFile('file');
            $fileName = $file->getRandomName();
            $data = array(
                'nm_aplikasi' => $this->request->getPost('nama'),
                'nm_sekolah' => $this->request->getPost('nm_sekolah'),
                'alamat' => $this->request->getPost('alamat'),
                'nm_kepsek' => $this->request->getPost('nm_kepsek'),
                'file' => $fileName
            );
            $file->move('image/', $fileName);
        }else{
            $data = array(
                'nm_aplikasi' => $this->request->getPost('nama'),
                'nm_sekolah' => $this->request->getPost('nm_sekolah'),
                'alamat' => $this->request->getPost('alamat'),
                'nm_kepsek' => $this->request->getPost('nm_kepsek'),
            );
        }
        
        //update data
        $success = $model->editSetting($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Setting');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Setting');
        }
    } 
    
    public function updatehari()
    {
        $model = new Hari_model;
        $id_hari1 = $this->request->getPost('hari1');
        $id_hari2 = $this->request->getPost('hari2');
        $id_hari3 = $this->request->getPost('hari3');
        $id_hari4 = $this->request->getPost('hari4');
        $id_hari5 = $this->request->getPost('hari5');
        $id_hari6 = $this->request->getPost('hari6');
        $id_hari7 = $this->request->getPost('hari7');

        $id_hari = $this->request->getPost('id_hari');
        $jammasuk = $this->request->getPost('jammasuk');
        $jampulang = $this->request->getPost('jampulang');
        $jml_data=count($id_hari);
        for ($i = 0; $i < $jml_data; $i++){
            $idhari = $id_hari[$i];
            $datahari = array(
                'jammasuk' => $jammasuk[$i],
                'jampulang' => $jampulang[$i]
            );
            //update data
            $success1 = $model->editHari($datahari, $idhari);
        }


        if($id_hari1==1){
            $id = $id_hari1;
            $data = array(
                'sts_hari' => 1
            );
            //update data
            $success = $model->editHari($data, $id);
        }else{
            $id = 1;
            $data = array(
                'sts_hari' => 2
            );
            //update data
            $success = $model->editHari($data, $id);
        }

        if($id_hari2==2){
            $id = $id_hari2;
            $data = array(
                'sts_hari' => 1
            );
            //update data
            $success = $model->editHari($data, $id);
        }else{
            $id = 2;
            $data = array(
                'sts_hari' => 2
            );
            //update data
            $success = $model->editHari($data, $id);
        }

        if($id_hari3==3){
            $id = $id_hari3;
            $data = array(
                'sts_hari' => 1
            );
            //update data
            $success = $model->editHari($data, $id);
        }else{
            $id = 3;
            $data = array(
                'sts_hari' => 2
            );
            //update data
            $success = $model->editHari($data, $id);
        }

        if($id_hari4==4){
            $id = $id_hari4;
            $data = array(
                'sts_hari' => 1
            );
            //update data
            $success = $model->editHari($data, $id);
        }else{
            $id = 4;
            $data = array(
                'sts_hari' => 2
            );
            //update data
            $success = $model->editHari($data, $id);
        }

        if($id_hari5==5){
            $id = $id_hari5;
            $data = array(
                'sts_hari' => 1
            );
            //update data
            $success = $model->editHari($data, $id);
        }else{
            $id = 5;
            $data = array(
                'sts_hari' => 2
            );
            //update data
            $success = $model->editHari($data, $id);
        }

        if($id_hari6==6){
            $id = $id_hari6;
            $data = array(
                'sts_hari' => 1
            );
            //update data
            $success = $model->editHari($data, $id);
        }else{
            $id = 6;
            $data = array(
                'sts_hari' => 2
            );
            //update data
            $success = $model->editHari($data, $id);
        }

        if($id_hari7==7){
            $id = $id_hari7;
            $data = array(
                'sts_hari' => 1
            );
            //update data
            $success = $model->editHari($data, $id);
        }else{
            $id = 7;
            $data = array(
                'sts_hari' => 2
            );
            //update data
            $success = $model->editHari($data, $id);
        }
       
        if($success){
            session()->setFlashdata('successhari','Diupdate');
            return redirect()->to('/Setting');
        }else{
            session()->setFlashdata('errorhari','Diupdate');
            return redirect()->to('/Setting');
        }
    }
   
}
