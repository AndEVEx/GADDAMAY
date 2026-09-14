<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class Temp extends Controller {
    public function index() {
        $db = \Config\Database::connect();
        try {
            print_r($db->query("SELECT t_ptk.id_ptk, t_ptk.nama_ptk, t_ptk.nomor_absensi, t_rombel.nm_rombel 
                                FROM t_ptk 
                                JOIN t_rombel ON t_rombel.id_guru_bk = t_ptk.id_ptk")->getResultArray());
        } catch(\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
