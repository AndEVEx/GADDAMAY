<?php

namespace App\Models;

use CodeIgniter\Model;

class User_model extends Model
{
    protected $table = 't_user';

    public function getUser()
    {
        return $this->db->table('t_user')
        ->orderBy('id_user', 'desc')
        ->get()->getResultArray();
    }

    public function saveUser($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }

    public function editUser($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_user', $id);
        return $builder->update($data);
    }
    public function editUseremail($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('username', $id);
        return $builder->update($data);
    }

    public function editPass($data, $id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_user', $id);
        return $builder->update($data);
    }

    public function hapusUser($id)
    {
        $builder = $this->db->table($this->table);
        return $builder->delete(['id_user' => $id]);
    }

    
}
