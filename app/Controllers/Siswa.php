<?php

namespace App\Controllers;
use App\Models\Siswa_model;
use App\Models\Siswarombel_model;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Dompdf\Dompdf;
use CodeIgniter\Controller;

class Siswa extends Controller
{
    public function index()
    {
        if(empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Siswa_model;
        
        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => 'Data Siswa',
            'nav' => 'Siswa'
        );

        try {
            $data = array(
                'getSiswa' => $model->getSiswa()
            );
            
            echo view('index/sidebar');
            echo view('func');
            echo view('index/navbar',  $datanav);
            echo view('master/siswa', $data);
            echo view('index/footer');
            
        } catch (\Throwable $e) {
            echo "SYSTEM ERROR: " . $e->getMessage() . " di file " . $e->getFile() . " baris " . $e->getLine();
        }
    }
   
    public function add()
    {
        
        $model = new Siswa_model;
        $file = $this->request->getFile('file');
        $fileName = $file->getRandomName();
        $data = array(
            'nisn' => $this->request->getPost('nisn'),
            'no_induk' => $this->request->getPost('no_induk'),
            'rfid' => $this->request->getPost('rfid'),
           
            'nm_siswa' => $this->request->getPost('nama'),
            'hp' => $this->request->getPost('hp'),
            'sts_siswa' => $this->request->getPost('sts'),
            'jk' => $this->request->getPost('jk'),
            'alamat' => $this->request->getPost('alamat'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tgl_lahir' => $this->request->getPost('tgl_lahir'),
            'file' => $fileName
        );

        //validasi input
        if(!$this->validate([
            "no_induk" => 'required|is_unique[t_siswa.no_induk]'
        ])){
            session()->setFlashdata('error','Ditambahkan, Nomor Induk tidak boleh sama');
            return redirect()->to('/Siswa');
        }
 
        $success = $model->saveSiswa($data);
        $file->move('image/siswa/', $fileName);
        if($success){
                session()->setFlashdata('success','Ditambahkan');
                return redirect()->to('/Siswa');
        }else{
                session()->setFlashdata('error','Ditambahkan');
                return redirect()->to('/Siswa');
        }
        
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
                'no_induk' => $this->request->getPost('no_induk'),
                'rfid' => $this->request->getPost('rfid'),
                'nm_siswa' => $this->request->getPost('nama'),
                'hp' => $this->request->getPost('hp'),
                'sts_siswa' => $this->request->getPost('sts'),
                'jk' => $this->request->getPost('jk'),
                'alamat' => $this->request->getPost('alamat'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tgl_lahir' => $this->request->getPost('tgl_lahir'),
                'file' => $fileName
            );
            $file->move('image/siswa/', $fileName);
        }else{
            $data = array(
                'nisn' => $this->request->getPost('nisn'),
                'no_induk' => $this->request->getPost('no_induk'),
                'rfid' => $this->request->getPost('rfid'),
                'nm_siswa' => $this->request->getPost('nama'),
                'hp' => $this->request->getPost('hp'),
                'sts_siswa' => $this->request->getPost('sts'),
                'jk' => $this->request->getPost('jk'),
                'alamat' => $this->request->getPost('alamat'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tgl_lahir' => $this->request->getPost('tgl_lahir')
            );
        }
        
       
        //update data
        $success = $model->editSiswa($data, $id);
        if($success){
            session()->setFlashdata('success','Diupdate');
            return redirect()->to('/Siswa');
        }else{
            session()->setFlashdata('error','Diupdate');
            return redirect()->to('/Siswa');
        }
    }

    public function hapus()
    {
        $model = new Siswa_model;
        $m_sisrombel = new Siswarombel_model;
        $id = $this->request->getPost('id');
        if (isset($id)) {
            //hapus data
            $success = $model->hapusSiswa($id);
            $success1 = $m_sisrombel->hapusSiswarombelidsiswa($id);
            if($success){
                session()->setFlashdata('success','Dihapus');
                return redirect()->to('/Siswa');
            }else{
                session()->setFlashdata('error','Dihapus');
                return redirect()->to('/Siswa');
            }
        } else {

            session()->setFlashdata('error','Dihapus, id data tidak di temukan');
            return redirect()->to('/Siswa');
        }
    }

    public function printkartu($id)
    {
        $model = new Siswa_model;
        $id_tapel = session()->get('id_tapel');

        //ambil logo
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM t_setting_aplikasi");
        $row = $query->getRow();

        $data = array(
            'getSiswa' => $model->getSiswaidtapel($id,$id_tapel),
            'getLogo' => $row->file,
            'getNama' => $row->nm_sekolah,
            'getAlamat' => $row->alamat,
            'getKepsek' => $row->nm_kepsek
        );
        echo view('func');
        echo view('print/kartu', $data);
      
    }

    public function printkartuall()
    {
        $model = new Siswa_model;
        $id_tapel = session()->get('id_tapel');

        //ambil logo
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM t_setting_aplikasi");
        $row = $query->getRow();

        $data = array(
            'getSiswa' => $model->getSiswaall($id_tapel),
            'getLogo' => $row->file,
            'getNama' => $row->nm_sekolah,
            'getAlamat' => $row->alamat,
            'getKepsek' => $row->nm_kepsek
        );
        echo view('func');
        echo view('print/kartuall', $data);
      
    }

    public function qr($kode)
    {
        // Pastikan sudah pakai namespace berikut di atas controller:
        // use Endroid\QrCode\Builder\Builder;
        // use Endroid\QrCode\Writer\PngWriter;

        $builder = new Builder(
            writer: new PngWriter(),
            data: $kode,
            size: 200,
            margin: 10,
        );

        $result = $builder->build();

        ob_clean(); // bersihkan buffer
        header('Content-Type: ' . $result->getMimeType());
        echo $result->getString();
        exit;
    }


    public function barcode($kode)
    {
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($kode, $generator::TYPE_CODE_128, 2, 60); // lebar x tinggi

        header('Content-Type: image/png');
        echo $barcode;
        exit;
    }

    // Controller
    public function downloadCard($nisn)
    {
        $model = new Siswa_model();
        $data['siswa'] = $model->findByNISN($nisn);

        $dompdf = new Dompdf();

        // load view
        $html = view('kartu_pelajar_pdf', $data);
        $dompdf->loadHtml($html);

        // Set ukuran kartu pelajar standar 86mm x 54mm
        $dompdf->setPaper([0, 0, 269.29, 153.07], 'landscape'); 
        // (1mm = 3.779528 px)

        // Render & download
        $dompdf->render();
        return $dompdf->stream("KartuPelajar_{$nisn}.pdf", ["Attachment" => true]);
    }


    /**
     * Bulk delete students by IDs
     */
    public function bulkDelete()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }

        $ids = $this->request->getPost('ids');
        if (empty($ids) || !is_array($ids)) {
            session()->setFlashdata('error', 'Tidak ada siswa yang dipilih');
            return redirect()->to('Siswa');
        }

        $db = \Config\Database::connect();
        $deleted = 0;
        foreach ($ids as $id) {
            // Delete from siswa_rombel first
            $db->table('t_siswa_rombel')->where('id_siswa', $id)->delete();
            // Delete from siswa
            $db->table('t_siswa')->where('id_siswa', $id)->delete();
            $deleted++;
        }

        session()->setFlashdata('success', "$deleted siswa berhasil dihapus");
        return redirect()->to('Siswa');
    }

}
