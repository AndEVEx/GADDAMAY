<?php

namespace App\Controllers;
use App\Models\Liburbesar_model;

use CodeIgniter\Controller;

class Liburbesar extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Liburbesar_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Libur Hari Besar',
            'nav' => 'Liburbesar'
        );

        $data = array(
            'getLiburbesar' => $model->getLiburbesar()
        );

        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/liburbesar', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Liburbesar_model;
        $total_dates = 0;
        $startDate = strtotime($this->request->getPost('tgl_mulai'));
        $endDate = strtotime($this->request->getPost('tgl_akhir'));
        for ( $currentDate = $startDate; $currentDate <= $endDate; $currentDate += (86400)) 
        {
                                
            $date = date('Y-m-d', $currentDate);

            //save into array
            $datesArray[] = $date;

            //count total dates between two dates
            $total_dates++; 
        }

        foreach ($datesArray as $key => $value) {
            $data = array(
                'keterangan' => $this->request->getPost('keterangan'),
                'tgl_libur' => $value
            );
            $success = $model->saveLiburbesar($data);
        }

        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Liburbesar');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Liburbesar');
        }
        
    }
    public function update()
    {
        $model = new Liburbesar_model;
        $id = $this->request->getPost('id');
        $data = array(
            'keterangan' => $this->request->getPost('keterangan'),
            'tgl_libur' => $this->request->getPost('tgl')
        );

        //update data
        $success = $model->editLiburbesar($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Liburbesar');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Liburbesar');
        }
    }
    public function hapus()
    {
        $model = new Liburbesar_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusLiburbesar($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Liburbesar');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Liburbesar');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Liburbesar');
        }
    }
}
