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

    public function getRombelsByTapel($id_tapel)
    {
        if(empty(session()->get('logged_in'))) {
            return $this->response->setJSON([]);
        }
        $db = \Config\Database::connect();
        $rombels = $db->table('t_rombel')
            ->where('id_tapel', $id_tapel)
            ->orderBy('nm_rombel', 'ASC')
            ->get()->getResultArray();
        return $this->response->setJSON($rombels);
    }

    public function promote()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }

        $source_tapel = $this->request->getPost('source_tapel');
        $source_rombel_id = $this->request->getPost('source_rombel');
        $target_tapel = session()->get('id_tapel');

        if (empty($source_tapel) || empty($source_rombel_id)) {
            session()->setFlashdata('error', 'Tahun Pelajaran Asal dan Rombel Asal harus diisi');
            return redirect()->to('/Siswarombel');
        }

        $db = \Config\Database::connect();

        if ($source_rombel_id === 'all') {
            // --- BATCH PROMOTION FOR ALL CLASSES ---
            $db->transStart();

            // 1. Delete all student-rombel assignments for Tingkat 3 (XII) classes in target tapel
            $db->query("DELETE FROM t_siswa_rombel WHERE id_tapel = ? AND id_rombel IN (SELECT id_rombel FROM t_rombel WHERE id_tingkat_kelas = 3 AND id_tapel = ?)", [$target_tapel, $target_tapel]);

            // 2. Delete all student-rombel assignments for Tingkat 2 (XI) classes in target tapel
            $db->query("DELETE FROM t_siswa_rombel WHERE id_tapel = ? AND id_rombel IN (SELECT id_rombel FROM t_rombel WHERE id_tingkat_kelas = 2 AND id_tapel = ?)", [$target_tapel, $target_tapel]);

            // 3. Delete all student-rombel assignments for Tingkat 1 (X) classes in target tapel (freshmen)
            $db->query("DELETE FROM t_siswa_rombel WHERE id_tapel = ? AND id_rombel IN (SELECT id_rombel FROM t_rombel WHERE id_tingkat_kelas = 1 AND id_tapel = ?)", [$target_tapel, $target_tapel]);

            // 3. Promote Tingkat 2 (XI) to Tingkat 3 (XII)
            $source_rombels_t2 = $db->table('t_rombel')
                ->where('id_tapel', $source_tapel)
                ->where('id_tingkat_kelas', 2)
                ->get()->getResultArray();

            $t3_promoted = 0;
            $t3_skipped = 0;

            foreach ($source_rombels_t2 as $s_rombel) {
                $source_name = $s_rombel['nm_rombel'];
                $target_name = null;
                if (strpos($source_name, 'XI ') === 0) {
                    $target_name = 'XII ' . substr($source_name, 3);
                } else {
                    $target_name = 'XII ' . $source_name;
                }

                $target_rombel = $db->table('t_rombel')
                    ->where('nm_rombel', $target_name)
                    ->where('id_tingkat_kelas', 3)
                    ->where('id_tapel', $target_tapel)
                    ->get()->getRowArray();

                if (!empty($target_rombel)) {
                    $target_rombel_id = $target_rombel['id_rombel'];
                    $students = $db->table('t_siswa_rombel')
                        ->where('id_rombel', $s_rombel['id_rombel'])
                        ->where('id_tapel', $source_tapel)
                        ->get()->getResultArray();

                    foreach ($students as $s) {
                        $exists = $db->table('t_siswa_rombel')
                            ->where('id_siswa', $s['id_siswa'])
                            ->where('id_tapel', $target_tapel)
                            ->countAllResults();

                        if ($exists == 0) {
                            $db->table('t_siswa_rombel')->insert([
                                'id_siswa' => $s['id_siswa'],
                                'id_rombel' => $target_rombel_id,
                                'id_tapel' => $target_tapel
                            ]);
                            $t3_promoted++;
                        } else {
                            $t3_skipped++;
                        }
                    }
                }
            }

            // 4. Promote Tingkat 1 (X) to Tingkat 2 (XI)
            $source_rombels_t1 = $db->table('t_rombel')
                ->where('id_tapel', $source_tapel)
                ->where('id_tingkat_kelas', 1)
                ->get()->getResultArray();

            $t2_promoted = 0;
            $t2_skipped = 0;

            foreach ($source_rombels_t1 as $s_rombel) {
                $source_name = $s_rombel['nm_rombel'];
                $target_name = null;
                if (strpos($source_name, 'X ') === 0) {
                    $target_name = 'XI ' . substr($source_name, 2);
                } else {
                    $target_name = 'XI ' . $source_name;
                }

                $target_rombel = $db->table('t_rombel')
                    ->where('nm_rombel', $target_name)
                    ->where('id_tingkat_kelas', 2)
                    ->where('id_tapel', $target_tapel)
                    ->get()->getRowArray();

                if (!empty($target_rombel)) {
                    $target_rombel_id = $target_rombel['id_rombel'];
                    $students = $db->table('t_siswa_rombel')
                        ->where('id_rombel', $s_rombel['id_rombel'])
                        ->where('id_tapel', $source_tapel)
                        ->get()->getResultArray();

                    foreach ($students as $s) {
                        $exists = $db->table('t_siswa_rombel')
                            ->where('id_siswa', $s['id_siswa'])
                            ->where('id_tapel', $target_tapel)
                            ->countAllResults();

                        if ($exists == 0) {
                            $db->table('t_siswa_rombel')->insert([
                                'id_siswa' => $s['id_siswa'],
                                'id_rombel' => $target_rombel_id,
                                'id_tapel' => $target_tapel
                            ]);
                            $t2_promoted++;
                        } else {
                            $t2_skipped++;
                        }
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Terjadi kesalahan database saat kenaikan kelas massal.');
            } else {
                session()->setFlashdata('success', "Kenaikan kelas massal berhasil! Promosi Tingkat XII: $t3_promoted siswa ($t3_skipped dilewati). Promosi Tingkat XI: $t2_promoted siswa ($t2_skipped dilewati).");
            }

            return redirect()->to('/Siswarombel');
        }

        // Get details of source rombel
        $source_rombel = $db->table('t_rombel')
            ->where('id_rombel', $source_rombel_id)
            ->get()->getRowArray();

        if (empty($source_rombel)) {
            session()->setFlashdata('error', 'Rombel Asal tidak ditemukan');
            return redirect()->to('/Siswarombel');
        }

        $source_name = $source_rombel['nm_rombel'];
        $source_tingkat = $source_rombel['id_tingkat_kelas'];

        // Determine target rombel name and tingkat
        $target_name = null;
        $target_tingkat = null;

        if ($source_tingkat == 1) {
            $target_tingkat = 2;
            if (strpos($source_name, 'X ') === 0) {
                $target_name = 'XI ' . substr($source_name, 2);
            } else {
                $target_name = 'XI ' . $source_name;
            }
        } elseif ($source_tingkat == 2) {
            $target_tingkat = 3;
            if (strpos($source_name, 'XI ') === 0) {
                $target_name = 'XII ' . substr($source_name, 3);
            } else {
                $target_name = 'XII ' . $source_name;
            }
        } else {
            session()->setFlashdata('error', 'Siswa di kelas Tingkat XII/3 tidak dapat dipromosikan (sudah lulus).');
            return redirect()->to('/Siswarombel');
        }

        // Find the target rombel in the current tapel with matching name and tingkat
        $target_rombel = $db->table('t_rombel')
            ->where('nm_rombel', $target_name)
            ->where('id_tingkat_kelas', $target_tingkat)
            ->where('id_tapel', $target_tapel)
            ->get()->getRowArray();

        if (empty($target_rombel)) {
            session()->setFlashdata('error', "Rombel Tujuan '$target_name' untuk Tahun Pelajaran Saat Ini tidak ditemukan. Silakan buat rombel '$target_name' terlebih dahulu.");
            return redirect()->to('/Siswarombel');
        }

        $target_rombel_id = $target_rombel['id_rombel'];

        // 1. Get all students from source rombel & tapel
        $students = $db->table('t_siswa_rombel')
            ->where('id_rombel', $source_rombel_id)
            ->where('id_tapel', $source_tapel)
            ->get()->getResultArray();

        if (empty($students)) {
            session()->setFlashdata('error', 'Tidak ada siswa ditemukan di Rombel Asal');
            return redirect()->to('/Siswarombel');
        }

        $insertedCount = 0;
        $skippedCount = 0;

        $db->transStart();
        foreach ($students as $s) {
            // Check if student already exists in any rombel for target tapel
            $exists = $db->table('t_siswa_rombel')
                ->where('id_siswa', $s['id_siswa'])
                ->where('id_tapel', $target_tapel)
                ->countAllResults();

            if ($exists == 0) {
                $db->table('t_siswa_rombel')->insert([
                    'id_siswa' => $s['id_siswa'],
                    'id_rombel' => $target_rombel_id,
                    'id_tapel' => $target_tapel
                ]);
                $insertedCount++;
            } else {
                $skippedCount++;
            }
        }
        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Terjadi kesalahan database saat kenaikan kelas.');
        } else {
            session()->setFlashdata('success', "Kenaikan kelas berhasil: $insertedCount siswa dari '$source_name' dipromosikan ke '$target_name', $skippedCount siswa dilewati (sudah memiliki kelas).");
        }

        return redirect()->to('/Siswarombel');
    }
}
