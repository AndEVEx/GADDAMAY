<?php

namespace App\Controllers;
use App\Models\Pegawai_model;
use App\Models\Jenisketenagaan_model;
use App\Models\Katgaji_model;
use App\Models\Gaji_model;

use CodeIgniter\Controller;

class Gajidosen extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_jenis = new Jenisketenagaan_model;
        $m_katgaji = new Katgaji_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Gaji Pegawai',
            'nav' => 'Gajidosen'
        );
        if(empty($this->request->getVar('jenis'))){
            $data = array(
                'getPegawai' => $model->getPegawaiall(),
                'getJenis' => $m_jenis->getJenis(),
                'getKatgaji' => $m_katgaji->getKatgaji(),
                'getBulan' => date('m'),
                'getTahun' => date('Y'),
                'Jenis' => 'All'
            );
        }else{
            $jenis = $this->request->getVar('jenis');
            $bln = $this->request->getVar('bln');
            if($jenis=='All'){
                $data = array(
                    'getPegawai' => $model->getPegawaiall(),
                    'getJenis' => $m_jenis->getJenis(),
                    'getKatgaji' => $m_katgaji->getKatgaji(),
                    'getBulan' => $bln,
                    'getTahun' => date('Y'),
                    'Jenis' => $jenis
                );
            }else{
                $data = array(
                    'getPegawai' => $model->getPegawaipil($jenis),
                    'getJenis' => $m_jenis->getJenis(),
                    'getKatgaji' => $m_katgaji->getKatgaji(),
                    'getBulan' => $bln,
                    'getTahun' => date('Y'),
                    'Jenis' => $jenis
                );
            }
            
        }

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/gajidosen', $data);
        echo view('index/footer');
    }
    public function global()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_jenis = new Jenisketenagaan_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Gaji Pegawai',
            'nav' => 'Gajidosen/global'
        );
        if(empty($this->request->getVar('jenis'))){
            $data = array(
                'getPegawai' => $model->getPegawaiall(),
                'getJenis' => $m_jenis->getJenis(),
                'getBulan' => date('m'),
                'getTahun' => date('Y'),
                'Jenis' => 'All'
            );
        }else{
            $jenis = $this->request->getVar('jenis');
            $bln = $this->request->getVar('bln');
            if($jenis=='All'){
                $data = array(
                    'getPegawai' => $model->getPegawaiall(),
                    'getJenis' => $m_jenis->getJenis(),
                    'getBulan' => $bln,
                    'getTahun' => date('Y'),
                    'Jenis' => $jenis
                );
            }else{
                $data = array(
                    'getPegawai' => $model->getPegawaipil($jenis),
                    'getJenis' => $m_jenis->getJenis(),
                    'getBulan' => $bln,
                    'getTahun' => date('Y'),
                    'Jenis' => $jenis
                );
            }
            
        }

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/gajiall', $data);
        echo view('index/footer');
    }
    
    public function add()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Gaji_model;
        $id = $this->request->getPost('id');
        $jenis = $this->request->getPost('jenis');
        $bln = $this->request->getPost('bln');
        $thn = $this->request->getPost('thn');
        $id_kat = $this->request->getPost('id_kat');
        $nominal = $this->request->getPost('nominal');
        $jml_data=count($nominal);

        for ($i = 0; $i < $jml_data; $i++){
            $data = [
                
                [
                    'id_ptk' => $id,
                    'id_kat_gaji' => $id_kat[$i],
                    'nominal' => $nominal[$i],
                    'bln' => $this->request->getPost('bln'),
                    'thn' =>$this->request->getPost('thn'),
                    'tgl_entri' => date('Y-m-d'),
                    'id_user' => session()->get('id_user')
                ],
                
            ];

        //insert data
        $success = $model->saveGaji($data);
        }

        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Gajidosen?jenis='.$jenis.'&bln='.$bln.'&thn='.$thn);
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Gajidosen?jenis='.$jenis.'&bln='.$bln.'&thn='.$thn);
        }
        
    }
    public function update()
    {
        $model = new Gaji_model;
        $id = $this->request->getPost('id');
        $jenis = $this->request->getPost('jenis');
        $bln = $this->request->getPost('bln');
        $thn = $this->request->getPost('thn');
        $id_kat = $this->request->getPost('id_kat');
        $nominal = $this->request->getPost('nominal');
        $jml_data=count($nominal);
        for ($i = 0; $i < $jml_data; $i++){
            $data = [
                
                [
                    'id_ptk' => $id,
                    'id_kat_gaji' => $id_kat[$i],
                    'nominal' => $nominal[$i],
                    'bln' => $this->request->getPost('bln'),
                    'thn' =>$this->request->getPost('thn')
                ],
                
            ];

        //update data
        $success = $model->editGaji($data);
        }
        
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Gajidosen?jenis='.$jenis.'&bln='.$bln.'&thn='.$thn);
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Gajidosen?jenis='.$jenis.'&bln='.$bln.'&thn='.$thn);
        }
    }
   
    public function hapus()
    {
        $model = new Gaji_model;
        $id = $this->request->getPost('id');
        $bln = $this->request->getPost('bln');
        $thn = $this->request->getPost('thn');
        $jenis = $this->request->getPost('jenis');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusGaji($id,$bln,$thn);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Gajidosen?jenis='.$jenis.'&bln='.$bln.'&thn='.$thn);
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Gajidosen?jenis='.$jenis.'&bln='.$bln.'&thn='.$thn);
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Gajidosen?jenis='.$jenis.'&bln='.$bln.'&thn='.$thn);
        }
    }
    public function copy()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Gaji_model;
        $jenis = $this->request->getPost('jenis');
        $bln = $this->request->getPost('bln');
        $thn = $this->request->getPost('thn');
        $bln_sebelum = $bln-1;

        //cek apakah ada gaji dibulan sebelumnya
        $db = \Config\Database::connect();
        $builder = $db->table('gaji');
        $builder->where('bln', $bln_sebelum);
        $builder->where('thn', $thn);
        $all =  $builder->countAllResults();

        if($all>0){
            //ambil semua data di gaji pada bulan sebelumnya
            
            $builder1 = $db->table('gaji');
            $builder1->select('id_ptk,id_kat_gaji,nominal');
            $builder1->where('bln', $bln_sebelum);
            $query = $builder1->get();
            foreach ($query->getResult() as $row) {
                $data = array(
                    'id_ptk' => $row->id_ptk,
                    'id_kat_gaji' => $row->id_kat_gaji,
                    'nominal' => $row->nominal,
                    'bln' => $bln,
                    'thn' => $thn,
                    'tgl_entri' => date('Y-m-d'),
                    'id_user' => session()->get('id_user')
                );
                //insert data
                $success = $model->saveGaji($data);
            }
        }else{
            session()->setFlashdata('error','Ditambahkan, Data gaji bulan sebelumnya terdeteksi kosong');
            return redirect()->to('/Gajidosen?jenis='.$jenis.'&bln='.$bln.'&thn='.$thn);
        }

        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Gajidosen?jenis='.$jenis.'&bln='.$bln.'&thn='.$thn);
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Gajidosen?jenis='.$jenis.'&bln='.$bln.'&thn='.$thn);
        }
        
    }
    public function report()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_katgaji = new Katgaji_model;
        $bln = $this->request->getPost('bln');
        $id = $this->request->getPost('id_ptk');

        if(empty($this->request->getVar('id_ptk'))){
            $nm = 0;
        
        }else{
            //ambil nama pegawai
            $db = \Config\Database::connect();
            $query = $db->query("SELECT nama_ptk FROM t_ptk where id_ptk='$id'");
            $row = $query->getRow();
            $nm = $row->nama_ptk;
        }
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Laporan Gaji Per Pegawai',
            'nav' => 'Gajidosen/report'
        );

        $data = array(
            'getPegawai' =>$model->getPegawaiall(),
            'getKatgaji' => $m_katgaji->getKatgaji(),
            'getBulan' =>$bln,
            'getTahun' =>date('Y'),
            'getNama' =>$nm,
            'getId' =>$id
        );

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/gajipegawai', $data);
        echo view('index/footer');
    }
    public function pegawai()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_katgaji = new Katgaji_model;
        $bln = $this->request->getPost('bln');
        $id = session()->get('id_user');

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Laporan Gaji Pegawai',
            'nav' => 'Gajidosen/pegawai'
        );

        if(empty($this->request->getVar('bln'))){
            $bln = date('m');
        }else{
            $bln = $this->request->getVar('bln');
        }
        
        

        $data = array(
            'getKatgaji' => $m_katgaji->getKatgaji(),
            'getBulan' =>$bln,
            'getTahun' =>date('Y'),
            'getNama' =>session()->get('nama'),
            'getId' =>$id
        );

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/gajiperpegawai', $data);
        echo view('index/footer');
    }
    public function cetakpegawai()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_katgaji = new Katgaji_model;
        $bln = $this->request->getVar('bln');
        $thn = $this->request->getVar('thn');
        $id = $this->request->getVar('id');

        //ambil nama pegawai
        $db = \Config\Database::connect();
        $query = $db->query("SELECT nama_ptk FROM t_ptk where id_ptk='$id'");
        $row = $query->getRow();
        $nm = $row->nama_ptk;

        $data = array(
            'getKatgaji' => $m_katgaji->getKatgaji(),
            'getBulan' =>$bln,
            'getTahun' =>date('Y'),
            'getNama' =>$nm,
            'getId' =>$id
        );

        echo view('func');
        echo view('print/gajipegawai', $data);
    }
    public function approve()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Gaji_model;
        $id_ptk = $this->request->getPost('approve');
        $jenis = $this->request->getPost('jenis');
        $bln = $this->request->getPost('bln');
        $thn = $this->request->getPost('thn');
        $jml_data=count($id_ptk);

        for ($i = 0; $i < $jml_data; $i++){
            $data = [
                
                [
                    'id_ptk' => $id_ptk[$i],
                    'bln' => $this->request->getPost('bln'),
                    'thn' => $this->request->getPost('thn'),
                    'tgl_approve' => date('Y-m-d'),
                    'id_user' => session()->get('id_user')
                ],
                
            ];

        //insert data
        $success = $model->saveGajiapprove($data);
        }

        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Gajidosen?jenis='.$jenis.'&bln='.$bln.'&thn='.$thn);
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Gajidosen?jenis='.$jenis.'&bln='.$bln.'&thn='.$thn);
        }
        
    }
}
