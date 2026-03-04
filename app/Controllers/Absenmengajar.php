<?php

namespace App\Controllers;
use App\Models\Absenmengajar_model;
use CodeIgniter\HTTP\RequestInterface;

use CodeIgniter\Controller;

class Absenmengajar extends Controller
{
    
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $m_absen = new Absenmengajar_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Absen Mengajar',
            'nav' => 'Absenmengajar'
        );

        //mendeteksi perangkat
        $agent = $this->request->getUserAgent();

        $data = array(
            'getAgent' => $agent
        );

        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/absenmengajar', $data);
        echo view('index/footer_absen');
    }
    public function report()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $m_absen = new Absenmengajar_model;
        $id_dosen = session()->get('id_user');
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Info Absen Mengajar',
            'nav' => 'Absenmengajar/report'
        );
        if(empty($this->request->getVar('bln'))){
            $bln = date('m');
            $data = array(
                'getAbsenmengajar' => $m_absen->getAbsenmengajardosen($bln,$id_dosen),
                'getBulan' => ""
            );
        }else{
            $bln = $this->request->getVar('bln');
            $data = array(
                'getAbsenmengajar' => $m_absen->getAbsenmengajardosen($bln,$id_dosen),
                'getBulan' => $bln
            );
        }

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/absenmengajar', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Absenmengajar_model;
        $folderPath = "image/dosen/";
        $img =$this->request->getPost('file');
        $image_parts = explode(";base64,", $img);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = uniqid() . '.png';

        $data = array(
            'id_dosen' => session()->get('id_user'),
            'nm_kegiatan' => $this->request->getPost('nama'),
            'catatan' => $this->request->getPost('catatan'),
            'foto' => $fileName,
            'longtitude' => $this->request->getPost('latlong'),
            'tgl_entri' => date('Y-m-d')
        );
        $file = $folderPath . $fileName;
        file_put_contents($file, $image_base64);
       
        $success = $model->saveAbsenmengajar($data);
        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Absenmengajar');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Absenmengajar');
        }
        
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
            return redirect()->to('/Absenmengajar/report?bln='.$bln);
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Absenmengajar/report?bln='.$bln);
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
                return redirect()->to('/Absenmengajar/report?bln='.$bln);
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Absenmengajar/report?bln='.$bln);
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Absenmengajar/report?bln='.$bln);
        }
    }
}
