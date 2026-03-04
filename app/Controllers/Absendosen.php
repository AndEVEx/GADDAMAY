<?php

namespace App\Controllers;
use App\Models\Absenmengajar_model;
use CodeIgniter\HTTP\RequestInterface;

use CodeIgniter\Controller;

class Absendosen extends Controller
{
    
    
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $m_absen = new Absenmengajar_model;
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Absen Mengajar Dosen',
            'nav' => 'Absendosen'
        );
        if(empty($this->request->getVar('bln'))){
            $bln = date('m');
            $data = array(
                'getAbsenmengajar' => $m_absen->getAbsenmengajar($bln),
                'getBulan' => ""
            );
        }else{
            $bln = $this->request->getVar('bln');
            $data = array(
                'getAbsenmengajar' => $m_absen->getAbsenmengajar($bln),
                'getBulan' => $bln
            );
        }

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/absenmengajardosen', $data);
        echo view('index/footer');
    }
    public function map()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
       
        echo view('index/sidebar');
        echo view('report/map');

    }
   
   
    public function update()
    {
        $model = new Absenmengajar_model;
        $id = $this->request->getPost('id');
        $bln = $this->request->getPost('bln');
        $data = array(
            'nm_kegiatan' => $this->request->getPost('nama'),
            'catatan' => $this->request->getPost('catatan')
        );

        //update data
        $success = $model->editAbsenmengajar($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Absendosen?bln='.$bln);
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Absendosen?bln='.$bln);
        }
    }
    public function hapus()
    {
        $model = new Absenmengajar_model;
        $id = $this->request->getPost('id');
        $bln = $this->request->getPost('bln');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusAbsenmengajar($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Absendosen?bln='.$bln);
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Absendosen?bln='.$bln);
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Absendosen?bln='.$bln);
        }
    }
}
