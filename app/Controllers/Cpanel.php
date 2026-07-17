<?php

namespace App\Controllers;
use App\Models\Tapel_model;

use CodeIgniter\Controller;

class Cpanel extends Controller
{
    public function index()
    {
        $model = new Tapel_model;

         //ambil logo
         $db = \Config\Database::connect();
         $query = $db->query("SELECT file FROM t_setting_aplikasi");
         $row = $query->getRow();

        $data = array(
            'getTapel' => $model->getTapelaktif(),
            'getLogo' => $row->file
        );

       

        echo view('index/sidebar_login');
        echo view('index/navbar_login', $data);
        echo view('index/footer_login');
    }
     
    public function process()
    {
        
        $db = \Config\Database::connect();
       
        $email = $this->request->getVar('email');
        $pass = $this->request->getVar('password');

        $id_tapel = $this->request->getVar('tapel');
        if($id_tapel==0){
            session()->setFlashdata('error', 'Tapel belum di pilih');
            return redirect()->back();
        }

        //merubah id tapel ke nama tapel
        $builder_tap = $db->table('r_tapel');
        $builder_tap -> where('id_tapel', $id_tapel);
        $query_tap =  $builder_tap->get();
        $tapel = $query_tap->getRow();


        $builder = $db->table('t_user');
        $builder -> where('username', $email);
        $query =  $builder->get();
        $user = $query->getRow();
        if($user){
            if (password_verify($pass, $user->password)) {
                session()->set([
                    'username' => $user->username,
                    'id_user' => $user->id_user,
                    'nama' => $user->nama,
                    'level' => $user->level,
                    'foto' => $user->foto,
                    'tapel' => $tapel->nm_tapel, 
                    'id_tapel' => $id_tapel,
                    'logged_in' => true
                    
                ]);
                $id = $user->id_user;
                return redirect()->to(base_url('Home'));
            } else {
                session()->setFlashdata('error', 'Password Salah');
                return redirect()->back();
            }
        }else{
            //cek guru - try multiple fields with TRIM to handle whitespace
            $email_trimmed = trim($email);
            // Try nip
            $user_gr = $db->query("SELECT * FROM t_ptk WHERE TRIM(nip) = ? LIMIT 1", [$email_trimmed])->getRow();

            // Fallback: try nik
            if(!$user_gr){
                $user_gr = $db->query("SELECT * FROM t_ptk WHERE TRIM(nik) = ? LIMIT 1", [$email_trimmed])->getRow();
            }
            if($user_gr){
                if (password_verify($pass, $user_gr->password)) {
                    session()->set([
                        'username' => $user_gr->nip,
                        'id_user' => $user_gr->id_ptk,
                        'nama' => $user_gr->nama_ptk,
                        'level' => 2,
                        'foto' => $user_gr->photo,
                        'tapel' => $tapel->nm_tapel, 
                        'id_tapel' =>$id_tapel,
                        'logged_in' => true
                        
                    ]);
                  
                    return redirect()->to(base_url('Home'));
                } else {
                    session()->setFlashdata('error', 'Password Salah');
                    return redirect()->back();
                }
            }else{
                //cek siswa
                $builder_sis = $db->table('t_siswa');
                $builder_sis -> where('no_induk', $email);
                $query_sis =  $builder_sis->get();
                $user_sis = $query_sis->getRow();

                if($user_sis){
                    session()->set([
                        'username' => $user_sis->no_induk,
                        'id_user' => $user_sis->id_siswa,
                        'nama' => $user_sis->nm_siswa,
                        'level' => 3,
                        'foto' => $user_sis->file,
                        'tapel' => $tapel->nm_tapel, 
                        'id_tapel' => $id_tapel,
                        'logged_in' => true
                    ]);
                    
                    return redirect()->to(base_url('Home'));
                }else{
                session()->setFlashdata('error', 'Username Tidak Terdaftar');
                return redirect()->back();
                }
            }
            
          
        }
        
    }
    
    function logout()
    {
        session()->destroy();
        return redirect()->to('Cpanel');
    }

    /**
     * Admin diagnostic: check if a NIP/username exists in database
     * Only accessible when logged in as admin (level 1)
     * Usage: /Cpanel/checkNip?q=197609052001122001
     */
    public function checkNip()
    {
        if(empty(session()->get('logged_in')) || session()->get('level') != 1){
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $q = trim($this->request->getGet('q') ?? '');
        if(empty($q)){
            return $this->response->setJSON(['error' => 'Parameter q required']);
        }

        $db = \Config\Database::connect();

        // Search in t_ptk
        $results = $db->query("SELECT id_ptk, nip, nik, nomor_absensi, nama_ptk, 
            (CASE WHEN password IS NOT NULL AND password != '' THEN 'YES' ELSE 'NO' END) as has_password
            FROM t_ptk 
            WHERE TRIM(nip) = ? OR TRIM(nomor_absensi) = ? OR TRIM(nik) = ?
            LIMIT 5", [$q, $q, $q])->getResultArray();

        // Search in t_user
        $userResults = $db->query("SELECT id_user, username, nama, level 
            FROM t_user WHERE TRIM(username) = ? LIMIT 5", [$q])->getResultArray();

        return $this->response->setJSON([
            'query' => $q,
            't_ptk_matches' => $results,
            't_user_matches' => $userResults,
            'ptk_count' => count($results),
            'user_count' => count($userResults)
        ]);
    }

    /**
     * Admin: bulk reset passwords for all wali kelas and BK
     * Only accessible when logged in as admin (level 1)
     */
    public function resetPasswordWaliBK()
    {
        if(empty(session()->get('logged_in')) || session()->get('level') != 1){
            session()->setFlashdata('error', 'Unauthorized');
            return redirect()->back();
        }

        $db = \Config\Database::connect();
        $id_tapel = session()->get('id_tapel');
        $hashWalas = password_hash('passwordwalas', PASSWORD_DEFAULT);
        $hashBK = password_hash('passwordbk', PASSWORD_DEFAULT);

        // Update wali kelas passwords
        $walasIds = $db->query("SELECT DISTINCT id_walikelas FROM t_rombel WHERE id_walikelas IS NOT NULL AND id_tapel = ?", [$id_tapel])->getResultArray();
        $walasCount = 0;
        foreach($walasIds as $w){
            $db->query("UPDATE t_ptk SET password = ? WHERE id_ptk = ?", [$hashWalas, $w['id_walikelas']]);
            $walasCount++;
        }

        // Update BK passwords
        $bkIds = $db->query("SELECT DISTINCT id_guru_bk FROM t_rombel WHERE id_guru_bk IS NOT NULL AND id_tapel = ?", [$id_tapel])->getResultArray();
        $bkCount = 0;
        foreach($bkIds as $b){
            $db->query("UPDATE t_ptk SET password = ? WHERE id_ptk = ?", [$hashBK, $b['id_guru_bk']]);
            $bkCount++;
        }

        session()->setFlashdata('success', "Password berhasil direset: $walasCount Wali Kelas (passwordwalas) dan $bkCount Guru BK (passwordbk)");
        return redirect()->to('Pegawai');
    }
    
}
