<?php

namespace App\Controllers;
use App\Models\Siswa_model;
use CodeIgniter\Controller;
 
class Siswaprofile extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswa_model;
        $id = session()->get('id_user');
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Profile',
            'nav1' => 'Profile'
        );
        
        $data = array(
            'getSiswa' => $model->getSiswaid($id)
        );
        
        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar',  $datanav);
        echo view('master/profilesiswa', $data);
        echo view('index/footer');
    }
    public function update()
    {
        $model = new Siswa_model;
        $id = $this->request->getPost('id');
        if($this->request->getFile('file')->isValid()){
            $file = $this->request->getFile('file');
            $fileName = $file->getRandomName();
            $data = array(
                'nisn' => $this->request->getPost('nisn'),
                'nm_siswa' => $this->request->getPost('nama'),
                'hp' => $this->request->getPost('hp'),
                'alamat' => $this->request->getPost('alamat'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tgl_lahir' => $this->request->getPost('tgl_lahir'),
                'file' => $fileName
            );
            $file->move('image/siswa/', $fileName);
        }else{
            $data = array(
                'nisn' => $this->request->getPost('nisn'),
                'nm_siswa' => $this->request->getPost('nama'),
                'hp' => $this->request->getPost('hp'),
                'alamat' => $this->request->getPost('alamat'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tgl_lahir' => $this->request->getPost('tgl_lahir')
            );
        }
        
       
        //update data
        $success = $model->editSiswa($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Siswaprofile');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Siswaprofile');
        }
    }
    public function updatepassword()
    {
        $model = new Siswa_model;
        $id = $this->request->getPost('id');
        $pass1=$this->request->getPost('password1');
        $pass2=$this->request->getPost('password2');

        if($pass1==$pass2)
        {
            $data = array(
                'password' => password_hash($pass1,PASSWORD_DEFAULT)
            );

            //update data
            $success = $model->editPegawai($data, $id);
            if($success){
                session()->setFlashdata('successpass','Diupdate');
                return redirect()->to('/Profile');
            }else{
                session()->setFlashdata('errorpass','Diupdate');
                return redirect()->to('/Profile');
            }
        }else
        {
            session()->setFlashdata('errorpass','Diupadete, terdeteksi password tidak sama');
            return redirect()->to('/Profile');
        }
    }
    public function printidcard()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswa_model;
        $writer = new PngWriter();
        $id = session()->get('id_user');
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Profile',
            'nav1' => 'Profile'
        );
        //cek data karyawan
        $db = \Config\Database::connect();
        $query = $db->query("SELECT no_induk FROM t_siswa where id_siswa='$id'");
        $row = $query->getRow();
        $noabs = $row->no_induk;

        // Create QR code
        $qrCode = QrCode::create($noabs)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(100)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Create generic logo
        $logo = Logo::create( FCPATH .'logo.png')
            ->setResizeToWidth(0);

        // Create generic label
        $label = Label::create('')
            ->setTextColor(new Color(255, 0, 0));

        $result = $writer->write($qrCode, $logo, $label);
        
        $dataUri = $result->getDataUri();
  
        $data = array(
            'getSiswa' => $model->getSiswaid($id),
            'getUri' => $dataUri
        );
        
       
        echo view('func');
        echo view('print/idcardsiswa', $data);
    }
    public function printidcardadmin()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswa_model;
        $writer = new PngWriter();
        $id = $this->request->getPost('id');
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Profile',
            'nav1' => 'Profile'
        );
        //cek data karyawan
        $db = \Config\Database::connect();
        $query = $db->query("SELECT no_induk FROM t_siswa where id_siswa='$id'");
        $row = $query->getRow();
        $noabs = $row->no_induk;

        // Create QR code
        $qrCode = QrCode::create($noabs)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(100)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Create generic logo
        $logo = Logo::create( FCPATH .'logo.png')
            ->setResizeToWidth(0);

        // Create generic label
        $label = Label::create('')
            ->setTextColor(new Color(255, 0, 0));

        $result = $writer->write($qrCode, $logo, $label);
        
        $dataUri = $result->getDataUri();
  
        $data = array(
            'getSiswa' => $model->getSiswaid($id),
            'getUri' => $dataUri
        );
        
       
        echo view('func');
        echo view('print/idcardsiswa', $data);
    }
}
