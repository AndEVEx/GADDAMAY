<?php

namespace App\Controllers;
use App\Models\Absensi_model;
use App\Models\Jenisketenagaan_model;
use App\Models\Pegawai_model;
use App\Models\Shift_model;
use App\Models\Lembur_model;
use CodeIgniter\Controller;

class Lembur extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $db = \Config\Database::connect();
        $model = new Absensi_model;
        $m_jenis = new Jenisketenagaan_model;
        $m_shift = new Shift_model;
        $m_pegawai = new Pegawai_model;
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Approve Lembur',
            'nav' => 'Lembur'
        );
       
        //ambil data batas lembur
        $query = $db->query("SELECT jam_lembur FROM t_setting_aplikasi");
        $row = $query->getRow();
        $jam = $row->jam_lembur;

        if(empty($this->request->getPost('jenis'))){
            $tgl = date('Y-m-d');
            $data = array(
                'getAbsensi' => $model->getAbsensilembur($tgl),
                'getJenis' => $m_jenis->getJenis(),
                'getShift' => $m_shift->getShift(),
                'getTanggal' => $tgl,
                'getBatasjam' => $jam
            );
        }else{
            $tgl = $this->request->getPost('tgl');
            $jenis = $this->request->getPost('jenis');
            $shift = $this->request->getPost('shift');
            if($jenis=="All" AND $shift=="All"){
                $data = array(
                    'getAbsensi' => $model->getAbsensilembur($tgl),
                    'getJenis' => $m_jenis->getJenis(),
                    'getShift' => $m_shift->getShift(),
                    'getTanggal' => $tgl,
                    'getBatasjam' => $jam
                );
            }elseif($jenis=="All"){
                $data = array(
                    'getAbsensi' => $model->getAbslemburshift($tgl,$shift),
                    'getJenis' => $m_jenis->getJenis(),
                    'getShift' => $m_shift->getShift(),
                    'getTanggal' => $tgl,
                    'getBatasjam' => $jam
                );
            }elseif($shift=="All"){
                $data = array(
                    'getAbsensi' => $model->getAbslemburjenis($tgl,$jenis),
                    'getJenis' => $m_jenis->getJenis(),
                    'getShift' => $m_shift->getShift(),
                    'getTanggal' => $tgl,
                    'getBatasjam' => $jam
                );
            }else{
                $data = array(
                    'getAbsensi' => $model->getAbslembur($tgl,$jenis,$shift),
                    'getJenis' => $m_jenis->getJenis(),
                    'getShift' => $m_shift->getShift(),
                    'getTanggal' => $tgl,
                    'getBatasjam' => $jam
                );
            }

        }

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/lembur', $data);
        echo view('index/footer');
    }
    public function report()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Lembur_model;
        $m_jenis = new Jenisketenagaan_model;
        $m_shift = new Shift_model;
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Report Lembur',
            'nav' => 'Lembur'
        );

        if(empty($this->request->getPost('jenis'))){
            $tgl = date('Y-m-d');
            $data = array(
                'getLembur' => $model->getLembur($tgl),
                'getJenis' => $m_jenis->getJenis(),
                'getShift' => $m_shift->getShift(),
                'getTanggal1' => $tgl,
                'getTanggal2' => $tgl
            );
        }else{
            $tgl1 = $this->request->getPost('tgl1');
            $tgl2 = $this->request->getPost('tgl2');
            $jenis = $this->request->getPost('jenis');
            $shift = $this->request->getPost('shift');
            if($jenis=="All" AND $shift=="All"){
                $data = array(
                    'getLembur' => $model->getLemburtgl($tgl1,$tgl2),
                    'getJenis' => $m_jenis->getJenis(),
                    'getShift' => $m_shift->getShift(),
                    'getTanggal1' => $tgl1,
                    'getTanggal2' => $tgl2
                );
            }elseif($jenis=="All"){
                $data = array(
                    'getLembur' => $model->getLemburtglshift($tgl1,$tgl2,$shift),
                    'getJenis' => $m_jenis->getJenis(),
                    'getShift' => $m_shift->getShift(),
                    'getTanggal1' => $tgl1,
                    'getTanggal2' => $tgl2
                );
            }elseif($shift=="All"){
                $data = array(
                    'getLembur' => $model->getLemburtgljenis($tgl1,$tgl2,$jenis),
                    'getJenis' => $m_jenis->getJenis(),
                    'getShift' => $m_shift->getShift(),
                    'getTanggal1' => $tgl1,
                    'getTanggal2' => $tgl2
                );
            }else{
                $data = array(
                    'getLembur' => $model->getLemburtglshiftjenis($tgl1,$tgl2,$jenis,$shift),
                    'getJenis' => $m_jenis->getJenis(),
                    'getShift' => $m_shift->getShift(),
                    'getTanggal1' => $tgl1,
                    'getTanggal2' => $tgl2
                );
            }

        }

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('report/lembur', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Lembur_model;
        $id_ptk = $this->request->getPost('approve');
        $jml_data=count($id_ptk);

        for ($i = 0; $i < $jml_data; $i++){
            $data = [
                
                [
                    'id_ptk' => $id_ptk[$i],
                    'tgl_lembur' => $this->request->getPost('tgl'),
                    'tgl_approve' => date('Y-m-d'),
                    'id_user' => session()->get('id_user')
                ],
                
            ];

        //insert data
        $success = $model->saveLembur($data);
        }

        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Lembur');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Lembur');
        }
        
    }
}
