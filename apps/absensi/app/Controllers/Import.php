<?php

namespace App\Controllers;
use App\Models\Pegawai_model;
use App\Models\Jenisketenagaan_model;
use App\Models\Siswa_model;
use App\Models\Siswarombel_model;
use App\Models\Rombel_model;

use CodeIgniter\Controller;

class Import extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Pegawai_model;
        $m_jenis = new Jenisketenagaan_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Import Data Master Guru dan Siswa',
            'nav' => 'Import'
        );

        $data = array(
            'getJenis' => $m_jenis->getJenis()
        );

        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/import', $data);
        echo view('index/footer');
    }
   
    public function add()
    {
        
        $model = new Pegawai_model;
        $jenis = $this->request->getPost('jenis');
        $file_excel = $this->request->getFile('file');
		$ext = $file_excel->getClientExtension();
		if($ext == 'xls') {
			$render = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
		} else {
			$render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		}
		$spreadsheet = $render->load($file_excel);
	
		$data = $spreadsheet->getActiveSheet()->toArray();
		foreach($data as $x => $row) {
			if ($x == 0) {
				continue;
			}
				
				$nip = $row[0];
	
				$db = \Config\Database::connect();
				$ceknip = $db->table('t_ptk')->getWhere(['nip'=>$nip])->getResult();

				if(count($ceknip) > 0) {
                    if($jenis==1){
                        $simpandata = [
                            'nip' => $row[0],
                            'nik' => $row[1],
                            'nama_ptk'=> $row[2],
                            'nama_panggilan'=> $row[3],
                            'id_divisi'=> $row[4],
                            'kd_jenis_kelamin'=> $row[5],
                            'id_jenis_ptk'=> $row[6],
                            'alamat'=> $row[7],
                            'no_hp'=> $row[8],
                            'tempat_lahir'=> $row[9],
                            'tgl_lahir'=> $row[10],
                            'tgl_join'=> $row[11],
                            'batas_cuti'=> $row[12],
                            'nomor_absensi'=> $row[13]
                        ];
            
                        $success = $model->editPegawainip($simpandata, $nip);
                    }else{
                        session()->setFlashdata('error','Import, NIP ada yang sama');
                        return redirect()->to('/Import');
                    }
					
				} else {
	
				$simpandata = [
					'nip' => $row[0],
                    'nik' => $row[1],
                    'nama_ptk'=> $row[2],
                    'nama_panggilan'=> $row[3],
                    'id_divisi'=> $row[4],
                    'kd_jenis_kelamin'=> $row[5],
                    'id_jenis_ptk'=> $row[6],
                    'alamat'=> $row[7],
                    'no_hp'=> $row[8],
                    'tempat_lahir'=> $row[9],
                    'tgl_lahir'=> $row[10],
                    'tgl_join'=> $row[11],
                    'batas_cuti'=> $row[12],
                    'nomor_absensi'=> $row[13],
                    'status_ptk'=> 1,
                    'password'=> password_hash(123,PASSWORD_DEFAULT)
				];
	
				$success = $model->savePegawai($simpandata);
			}
		}
        if($success){
            session()->setFlashdata('success','Import');
            return redirect()->to('/Import');
        }else{
                session()->setFlashdata('error','Import');
                return redirect()->to('/Import');
        }
		
    }
    public function addsiswa()
    {
        
        $model = new Siswa_model;
        $id_tapel = session()->get('id_tapel');
       
        $file_excel = $this->request->getFile('file');
		$ext = $file_excel->getClientExtension();
		if($ext == 'xls') {
			$render = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
		} else {
			$render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		}
		$spreadsheet = $render->load($file_excel);
	
		$data = $spreadsheet->getActiveSheet()->toArray();
		foreach($data as $x => $row) {
			if ($x == 0) {
				continue;
			}
				
				$nis = $row[0];
	
				$db = \Config\Database::connect();
				$ceknis = $db->table('t_siswa')->getWhere(['no_induk'=>$nis])->getResult();

                $simpandata = [
                    'no_induk' => $row[0],
                    'nisn' => $row[1],
                    'rfid' => $row[2],
                    'nm_siswa' => $row[3],
                    'alamat'=> $row[4],
                    'jk'=> $row[5],
                    'hp'=> $row[6],
                    'tempat_lahir'=> $row[7],
                    'tgl_lahir'=> $row[8],
                    'sts_siswa'=> 1
                    
                ];

				if(count($ceknis) > 0) {
                    $success = $model->editSiswaimport($simpandata, $nis);
				} else {
				    $success = $model->saveSiswa($simpandata);
			    }

                // If a 10th column (index 9) is provided, assign student directly to that rombel in target tapel
                if (isset($row[9]) && trim($row[9]) !== '') {
                    $rombel_val = trim($row[9]);
                    $id_rombel = null;
                    if (is_numeric($rombel_val)) {
                        $id_rombel = (int)$rombel_val;
                    } else {
                        // Lookup by name in current active tapel
                        $rombel_row = $db->table('t_rombel')
                            ->where('nm_rombel', $rombel_val)
                            ->where('id_tapel', $id_tapel)
                            ->get()->getRowArray();
                        if (!empty($rombel_row)) {
                            $id_rombel = $rombel_row['id_rombel'];
                        }
                    }

                    if ($id_rombel) {
                        $idsiswa_row = $db->table('t_siswa')->getWhere(['no_induk' => $nis])->getRow();
                        if ($idsiswa_row) {
                            $idsiswa = $idsiswa_row->id_siswa;
                            $cek_rombel = $db->table('t_siswa_rombel')
                                ->getWhere(['id_siswa' => $idsiswa, 'id_tapel' => $id_tapel])
                                ->getResult();

                            $rombel_data = [
                                'id_siswa' => $idsiswa,
                                'id_rombel' => $id_rombel,
                                'id_tapel' => $id_tapel
                            ];

                            if (count($cek_rombel) > 0) {
                                $db->table('t_siswa_rombel')
                                    ->where('id_siswa', $idsiswa)
                                    ->where('id_tapel', $id_tapel)
                                    ->update($rombel_data);
                            } else {
                                $db->table('t_siswa_rombel')->insert($rombel_data);
                            }
                        }
                    }
                }
		}

        if($success){
            session()->setFlashdata('success','Import');
            return redirect()->to('/Import');
        }else{
                session()->setFlashdata('error','Import');
                return redirect()->to('/Import');
        }
		
    }
    public function addsettingkelas()
    {
        
        $model = new Siswarombel_model;
       
        $file_excel = $this->request->getFile('file');
		$ext = $file_excel->getClientExtension();
		if($ext == 'xls') {
			$render = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
		} else {
			$render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		}
		$spreadsheet = $render->load($file_excel);
        $id_tapel = session()->get('id_tapel');
        echo view('func_siswa');
	
		$data = $spreadsheet->getActiveSheet()->toArray();
		foreach($data as $x => $row) {
			if ($x == 0) {
				continue;
			}
				
				$nis = $row[0];
                $idsiswa = idsiswa($nis);
	
				$db = \Config\Database::connect();
				$ceknis = $db->table('t_siswa')->getWhere(['no_induk'=>$nis])->getResult();
                $cekidsiswa = $db->table('t_siswa_rombel')->getWhere(['id_siswa'=>$idsiswa,'id_tapel'=>$id_tapel])->getResult();

                if(count($ceknis) > 0) {
                    $simpandata = [
                        'id_siswa' => $idsiswa,
                        'id_tapel' => $id_tapel,
                        'id_rombel'=> $row[1]
                    ];
    
                    if(count($cekidsiswa) > 0) {
                        $success = $model->editSiswarombelimport($simpandata, $idsiswa,$id_tapel);
                    }else{
                        $success = $model->saveSiswarombelimport($simpandata);
                    }
                }
                
		}

        if($success){
            session()->setFlashdata('success','Import');
            return redirect()->to('/Import');
        }else{
            session()->setFlashdata('error','Import');
            return redirect()->to('/Import');
        }
		
    }

    public function addrombel()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        
        $model = new Rombel_model;
       
        $file_excel = $this->request->getFile('file');
		$ext = $file_excel->getClientExtension();
		if($ext == 'xls') {
			$render = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
		} else {
			$render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		}
		$spreadsheet = $render->load($file_excel);
        $id_tapel = session()->get('id_tapel');
	
		$data = $spreadsheet->getActiveSheet()->toArray();
        $success = false;
		foreach($data as $x => $row) {
			if ($x == 0) {
				continue;
			}
				
            $nm_rombel = $row[0];
            $id_tingkat_kelas = $row[1];
            $nip_walikelas = $row[2];

            if(empty($nm_rombel)) continue;

            // Look up walikelas by NIP
            $db = \Config\Database::connect();
            $id_walikelas = null;
            if(!empty($nip_walikelas)) {
                $queryWali = $db->table('t_ptk')->getWhere(['nip' => $nip_walikelas])->getRow();
                if($queryWali) {
                    $id_walikelas = $queryWali->id_ptk;
                }
            }

            // Check if rombel already exists for this tapel
            $cekRombel = $db->table('t_rombel')
                ->where('nm_rombel', $nm_rombel)
                ->where('id_tapel', $id_tapel)
                ->get()->getResult();

            $simpandata = [
                'nm_rombel' => $nm_rombel,
                'id_tingkat_kelas' => $id_tingkat_kelas,
                'id_tapel' => $id_tapel,
                'id_walikelas' => $id_walikelas
            ];

            if(count($cekRombel) > 0) {
                // Update existing
                $db->table('t_rombel')
                    ->where('nm_rombel', $nm_rombel)
                    ->where('id_tapel', $id_tapel)
                    ->update($simpandata);
                $success = true;
            } else {
                // Insert new
                $success = $model->saveRombel($simpandata);
            }
		}

        if($success){
            session()->setFlashdata('success','Import Rombel berhasil');
            return redirect()->to('/Import');
        }else{
            session()->setFlashdata('error','Import Rombel gagal');
            return redirect()->to('/Import');
        }
		
    }
}
