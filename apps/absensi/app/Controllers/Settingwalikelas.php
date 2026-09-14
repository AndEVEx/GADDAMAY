<?php

namespace App\Controllers;
use App\Models\Settingwalikelas_model;
use App\Models\Rombel_model;
use App\Models\Walikelas_model;

use CodeIgniter\Controller;

class Settingwalikelas extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $m_rombel = new Rombel_model;
        $m_walikelas = new Walikelas_model;

        $id_tapel = session()->get('id_tapel');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Setting Wali Kelas',
            'nav' => 'Settingwalikelas'
        );

        $data = array(
            'getRombel' => $m_rombel->getRombelwalikelas($id_tapel),
            'getRombeltampil' => $m_rombel->getRombelnonwalikelas($id_tapel),
            'getWalikelas' => $m_walikelas->getWalikelassnonrombel($id_tapel)
        );
 
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/settingwalikelas', $data);
        echo view('index/footer');
    }
   
    public function addrombel()
    {
        
        $model = new Settingwalikelas_model;
        $id_walikelas = $this->request->getPost('id_walikelas');
        $id_rombel = $this->request->getPost('id_rombel');
        $jml_data=count($id_walikelas);

        for ($i = 0; $i < $jml_data; $i++){
            $data = [
                
                [
                    'id_walikelas' => $id_walikelas[$i],
                    'id_rombel' => $id_rombel[$i],
                    'id_tapel' => session()->get('id_tapel')
                ],
                
            ];

        //insert data
        
        $success = $model->saveSettingwalikelas($data);
        $successhps = $model->hapusSettingwalikelas1();
        }

        if($success){
                session()->setFlashdata('success1','Ditambahkan');
                return redirect()->to('/Settingwalikelas');
        }else{
                session()->setFlashdata('error1','Ditambahkan');
                return redirect()->to('/Settingwalikelas');
        }
        
    }
    
    public function hapus()
    {
        $model = new Settingwalikelas_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusSettingwalikelas($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Settingwalikelas');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Settingwalikelas');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Settingwalikelas');
        }
    }
}
