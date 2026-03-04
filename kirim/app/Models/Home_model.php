<?php

namespace App\Models;

use CodeIgniter\Model;

class Home_model extends Model
{
    protected $table = 't_transaksi';

    public function getHistory()
    {
        return $this->db->table($this->table)
        ->join('t_barang', 't_barang.id_barang = t_transaksi.id_barang')
        ->orderBy('id_transaksi', 'asc')
        ->get()->getResultArray();
    }

}
