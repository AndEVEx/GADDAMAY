<?php

namespace App\Controllers;
use App\Models\Siswarombel_model;
use App\Models\Rombel_model;
use App\Models\Siswa_model;

use CodeIgniter\Controller;

class Siswarombel extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswarombel_model;
        $m_siswa = new Siswa_model;
        $m_rombel = new Rombel_model;
        $id_tapel = session()->get('id_tapel');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Setting Kelas',
            'nav' => 'Siswarombel'
        );

        $data = array(
            'getSiswarombel' => $model->getSiswarombel($id_tapel),
            'getSiswa' => $m_siswa->getSiswasnonrombel(),
            'getRombel' => $m_rombel->getRombel($id_tapel),
            'getTapel' => $id_tapel
        );
  
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/siswarombel', $data);
        echo view('index/footer');
    }
    public function detail()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswarombel_model;
        $m_siswa = new Siswa_model;
        $m_rombel = new Rombel_model;
        $id_tapel = session()->get('id_tapel');
        $id = $this->request->getPost('id');

        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Siswa Kelas',
            'nav' => 'Siswarombel/detail'
        );

        $data = array(
            'getIdrombel' => $id,
            'getSiswarombel' => $model->getSiswarombel($id),
            'getRombel' => $m_rombel->getRombel($id_tapel)
        );

        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/datasiswarombel', $data);
        echo view('index/footer');
    }
   
    
    public function addrombel()
    {
        
        $model = new Siswarombel_model;
        $id_siswa = $this->request->getPost('id_siswa');
        $id_rombel = $this->request->getPost('id_rombel');
        $id_tapel = session()->get('id_tapel');
        $jml_data=count($id_siswa);

        for ($i = 0; $i < $jml_data; $i++){
            $data = [
                
                [
                    'id_siswa' => $id_siswa[$i],
                    'id_rombel' => $id_rombel[$i],
                    'id_tapel' => $id_tapel
                ],
                
            ];

        //insert data
        
        $success = $model->saveSiswarombel($data);
        $successhps = $model->hapusSiswarombel1();
        }

        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Siswarombel');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Siswarombel');
        }
        
    }
   
    public function updaterombel()
    {
        $model = new Siswarombel_model;
        $id = $this->request->getPost('id');
        $id_rombel = $this->request->getPost('id_rombel');
        $jml_data=count($id);

        for ($i = 0; $i < $jml_data; $i++){
            $id_siswa_rombel = $id[$i];
            $data = array(
                'id_rombel' => $id_rombel[$i]
            );
            //update data
            $success = $model->editSiswarombel($data, $id_siswa_rombel);
        }

        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Siswarombel');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Siswarombel');
        }
    }
    public function hapus()
    {
        $model = new Siswarombel_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusSiswarombel($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Siswarombel');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Siswarombel');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Siswarombel');
        }
    }
}
