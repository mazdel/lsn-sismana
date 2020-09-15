<?php namespace App\Models;

use CodeIgniter\Model;

class Dbcreator extends Model{
    public function __construct()
    {
        $this->db = db_connect(null,false);
    }
    public function select()
    {
        //return $this->db->query('select * from wp_users');
    }
}