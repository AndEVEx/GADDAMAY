<?php

namespace App\Controllers;
use App\Models\Point_model;
use App\Models\Totalpoint_model;
use App\Models\Pointsiswa_model;
use App\Models\Totalpointsiswa_model;
use App\Models\Tingkatkelas_model;

use CodeIgniter\Controller;

class Home extends Controller
{
    public function index()
    {
        if (empty(session()->get('logged_in'))) {
            return redirect()->to('Cpanel');
        }
        $model = new Totalpoint_model;
        $m_pointsis = new Totalpointsiswa_model;
        $m_tingkat = new Tingkatkelas_model;
        $id_tapel = session()->get('id_tapel');

        if ((session()->get('level') == 1 || session()->get('level')) == 4) {
            $nmdashboard = "Dashboard Administrator";
        } elseif ((session()->get('level')) == 2) {
            $nmdashboard = "Dashboard Guru";
        } elseif ((session()->get('level')) == 3) {
            $nmdashboard = "Dashboard Siswa";
        }

        $datanav = array(
            'nama' => session()->get('nama'),
            'title' => $nmdashboard,
            'nav1' => 'Home'
        );

        $data = array(
            'getPointkaryawan' => $model->getTotalpointterbaik(date('m'), date('Y')),
            'getPointsiswa' => $m_pointsis->getTotalpointsiswaterbaik(date('m'), $id_tapel),
            'getTingkat' => $m_tingkat->getTingkat()
        );

        echo view('index/sidebar');

        if ((session()->get('level')) == 3) {
            echo view('func_siswa');
        } else {
            echo view('func');
        }

        echo view('index/navbar', $datanav);
        if (session()->get('level') == 1 || session()->get('level') == 4) {
            echo view('home/home_admin', $data);
        } elseif ((session()->get('level')) == 2) {
            echo view('home/home_guru');
        } elseif ((session()->get('level')) == 3) {
            echo view('home/home_siswa');
        }
        echo view('index/footer');
        if (session()->get('level') == 1 || session()->get('level') == 4) {
            echo view('home/chart_kelas_harian');
        }
        if ((session()->get('level')) == 3) {
            echo view('home/chartsis');
        } elseif ((session()->get('level')) == 2) {
            echo view('home/chartguru');
        }
    }

}
