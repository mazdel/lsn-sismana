<?php namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\Encryption;

class Dbcreator extends Model{
    public function __construct()
    {
        $this->db       = db_connect(null,false);
        $this->dbforge  = \Config\Database::forge();
        $this->encrypt  = new Encryption();
    }
    public function create()
    {
        $this->create_table();
        $this->create_admin();
        return true;
    }
    /*create default user (admin) */
    public function create_admin()
    {
        $members = $this->db->table('member');
        $result = $members->get()->getResult();
        if($members->countAllResults()<1){
            $admin = [
                'username'  => 'admin',
                'password'  => $this->encrypt->oneway('adminganteng'),
                'level'     => 'admin'
            ];
            $result =  $members->insert($admin);
        }
        return $result;
    }
    /*create table if not exist*/
    public function create_table()
    {
        if($this->db_check()==false){
            return false;
        }
        /* add members field */
        $members = [
            'id'    =>[
                'type'              =>'INT',
                'constraint'        =>'5',
                'unsigned'          =>true,
                'auto_increment'    =>true,
                'null'              =>false
            ],
            'username'    =>[
                'type'              =>'VARCHAR',
                'constraint'        =>'45',
                'null'              =>true
            ],
            'nik'    =>[
                'type'              =>'varchar',
                'constraint'        =>'16',
                'null'              =>false,
                'unique'            =>true,
            ],
            'password'    =>[
                'type'              =>'VARCHAR',
                'constraint'        =>'45',
                'null'              =>true
            ],
            'telp'    =>[
                'type'              =>'varchar',
                'constraint'        =>'16',
                'null'              =>false
            ],
            'domisili_kec'    =>[
                'type'              =>'VARCHAR',
                'constraint'        =>'60',
                'null'              =>false
            ],
            'domisili_kab'    =>[
                'type'              =>'VARCHAR',
                'constraint'        =>'60',
                'null'              =>false
            ],
            'domisili_prov'    =>[
                'type'              =>'VARCHAR',
                'constraint'        =>'60',
                'null'              =>false
            ],
            'level'    =>[
                'type'              =>'ENUM',
                'constraint'        =>['admin','pengurus','anggota'],
                'default'           =>'anggota'
            ],
            'nama'    =>[
                'type'              =>'VARCHAR',
                'constraint'        =>'60',
                'null'              =>false
            ],
            'tgl_gabung datetime not null default current_timestamp',
            'foto_profil'    =>[
                'type'              =>'VARCHAR',
                'constraint'        =>'45',
                'null'              =>true
            ],
            'active'    =>[
                'type'              =>'ENUM',
                'constraint'        =>['Y','N'],
                'default'           =>'Y'
            ],
            'deleted'    =>[
                'type'              =>'ENUM',
                'constraint'        =>['Y','N'],
                'default'           =>'N'
            ],
        ];
        $this->dbforge->addField($members);
        $this->dbforge->addPrimaryKey('id');
        $this->dbforge->createTable('member',true);
        /* /add member fields */

        /*add system setting field */
        $settings = [
            'id'    =>[
                'type'              =>'INT',
                'constraint'        =>'5',
                'unsigned'          =>true,
                'auto_increment'    =>true,
                'null'              =>false
            ],
            'banner'    =>[
                'type'              =>'VARCHAR',
                'constraint'        =>'45',
                'null'              =>true
            ],
            'name'    =>[
                'type'              =>'varchar',
                'constraint'        =>'30',
                'null'              =>false
            ],
            'subname'    =>[
                'type'              =>'varchar',
                'constraint'        =>'30',
                'null'              =>true
            ],
            'date_created datetime not null default current_timestamp',
        ];
        $this->dbforge->addField($settings);
        $this->dbforge->addPrimaryKey('id');
        $this->dbforge->createTable('site_setting',true);
        /* /add site settings field */
        return true;
    }
    /*check is database exist */
    public function db_check()
    {
        $dbConfig   = config('database',false)->default;
        //return $dbConfig['database'];
        if($this->db->query('use '.$dbConfig['database'])){
            return true;
        }
        return false;
    }
}