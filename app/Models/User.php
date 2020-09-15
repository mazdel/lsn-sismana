<?php namespace App\Models;

use CodeIgniter\Model;

class User extends Model{
    public function __construct()
    {
        $this->db = db_connect(null,false);
    }
    public function add($data=null)
    {
        return false;
    }
}