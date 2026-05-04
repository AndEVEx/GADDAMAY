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
            //cek guru by nip
            $builder_gr = $db->table('t_ptk');
            $builder_gr -> where('nip', $email);
            $query_gr =  $builder_gr->get();
            $user_gr = $query_gr->getRow();
            // Fallback: try nomor_absensi
            if(!$user_gr){
                $builder_gr2 = $db->table('t_ptk');
                $builder_gr2 -> where('nomor_absensi', $email);
                $query_gr2 =  $builder_gr2->get();
                $user_gr = $query_gr2->getRow();
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
    
}
