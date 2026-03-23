<?php

namespace App\Controllers;
use App\Models\Point_model;
use App\Models\Totalpoint_model;
use App\Models\Pointsiswa_model;
use App\Models\Totalpointsiswa_model;
use App\Models\Tingkatkelas_model;
use App\Models\MuridMonitoring_model;

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
            // Check if guru is walikelas or BK
            $db = \Config\Database::connect();
            $id_user = session()->get('id_user');

            $isWali = $db->table('t_rombel')
                ->where('id_walikelas', $id_user)
                ->where('id_tapel', $id_tapel)
                ->countAllResults();

            $isBK = $db->table('t_rombel')
                ->where('id_guru_bk', $id_user)
                ->where('id_tapel', $id_tapel)
                ->countAllResults();

            if ($isWali > 0 || $isBK > 0) {
                return redirect()->to('MuridMonitoring');
            }

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
            // For kepsek, also load monitoring data
            $monitoringModel = new MuridMonitoring_model();
            $monitorList = $monitoringModel->getMonitoringAll($id_tapel);
            foreach ($monitorList as &$m) {
                $m['progress'] = $monitoringModel->getProgress($m['id_monitoring']);
            }
            unset($m);
            $data['monitorList'] = $monitorList;

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
