<?php

namespace App\Controllers;
use App\Models\Rombel_model;
use App\Models\Tingkatkelas_model;
use App\Models\Pegawai_model;

use CodeIgniter\Controller;

class Rombel extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Rombel_model;
        $m_tingkat = new Tingkatkelas_model;
        $m_pegawai = new Pegawai_model;
        $id_tapel = session()->get('id_tapel');
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Rombel',
            'nav' => 'Rombel'
        );

        $data = array(
            'getTingkat' => $m_tingkat->getTingkat(),
            'getRombel' => $model->getRombel($id_tapel),
            'getGuru' => $m_pegawai->getPegawaiwalikelas($id_tapel)
        );

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/rombel', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Rombel_model;
        $data = array(
            'nm_rombel' => $this->request->getPost('nama'),
            'id_tingkat_kelas' => $this->request->getPost('tingkat'),
            'id_tapel' => session()->get('id_tapel'),
            'id_walikelas' => $this->request->getPost('id_ptk')
        );

        //validasi input
        if(!$this->validate([
            "nama" => 'required|is_unique[t_rombel.nm_rombel]'
        ])){
            session()->setFlashdata('error','Ditambahkan, Nama Rombel tidak boleh sama');
            return redirect()->to('/Rombel');
        }

        $success = $model->saveRombel($data);
        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Rombel');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Rombel');
        }
        
    }
    public function update()
    {
        $model = new Rombel_model;
        $id = $this->request->getPost('id');
        $data = array(
            'nm_Rombel' => $this->request->getPost('nama'),
            'id_walikelas' => $this->request->getPost('id_ptk')
        );

        //update data
        $success = $model->editRombel($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Rombel');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Rombel');
        }
    }
    public function hapus()
    {
        $model = new Rombel_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusRombel($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Rombel');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Rombel');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Rombel');
        }
    }
}
