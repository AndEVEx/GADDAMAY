<?php

namespace App\Controllers;
use App\Models\Jenisketenagaan_model;
use App\Models\Pegawai_model;
use App\Models\Shift_model;
use App\Models\Libur_model;
use App\Models\Jadwalabsen_model;
use CodeIgniter\Controller;

class Jadwalsatpam extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Libur_model;
        $m_pegawai = new Pegawai_model;
        $m_shift = new Shift_model;
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Jadwal Satpam',
            'nav' => 'Jadwalsatpam'
        );
        $bln = $this->request->getVar('bln');
                                            
        if(isset($bln)){
            //jumlah hari
            $jml_hari = cal_days_in_month(CAL_GREGORIAN, $bln, date('Y')) ;
            $data = array(
                'getShift' => $m_shift->getShift(),
                'getPegawai' => $m_pegawai->getPegawaisatpam(),
                'getBulan' => $bln,
                'Jumlahhari' => $jml_hari
            );
        }else{ 
            $bulan = date('m');
            $jml_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, date('Y')) ;
            $data = array(
                'getPegawai' => $m_pegawai->getPegawaisatpam(),
                'getShift' => $m_shift->getShift(),
                'getBulan' => $bulan,
                'Jumlahhari' => $jml_hari
            );
        }

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('setting/jadwalsatpam', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Jadwalabsen_model;
        $bln = $this->request->getVar('bln');
        $shift = $this->request->getVar('shift');
        $jml_data=count($shift);
        for ($i = 0; $i < $jml_data; $i++){
            $tgl = date('Y').'-'.$bln.'-'.($i+1);
            $data = [
                
                [
                    'id_ptk' => $this->request->getPost('id_ptk'),
                    'tgl_jadwal' => $tgl,
                    'bulan' => $bln,
                    'id_shift' => $shift[$i]
                ],
                
            ];

        //insert data 
        $success = $model->saveJadwal($data);
        }

        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Jadwalsatpam/?bln='.$bln);
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Jadwalsatpam/?bln='.$bln);
        }
        
    }
    public function update()
    {
        $model = new Jadwalabsen_model;
        $bln = $this->request->getVar('bln');
        $shift = $this->request->getVar('shift');
        $jml_data=count($shift);
        for ($i = 0; $i < $jml_data; $i++){
            $tgl = date('Y').'-'.$bln.'-'.($i+1);
            $data = [
                
                [
                    'id_ptk' => $this->request->getPost('id_ptk'),
                    'tgl_jadwal' => $tgl,
                    'bulan' => $bln,
                    'id_shift' => $shift[$i]
                ],
                
            ];

        //update data
        $success = $model->editJadwal($data);
        }

        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Jadwalsatpam/?bln='.$bln);
        }else{
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Jadwalsatpam/?bln='.$bln);
        }
    }
    public function hapus()
    {
        $model = new Jadwalabsen_model;
        $id = $this->request->getPost('id');
        $bln = $this->request->getVar('bln');
       
        $thn = date('Y');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusJadwal($id,$bln,$thn);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Jadwalsatpam/?bln='.$bln);
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Jadwalsatpam/?bln='.$bln);
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Jadwalsatpam/?bln='.$bln);
        }
    }
}
