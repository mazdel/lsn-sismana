<?php namespace App\Libraries;

//use CodeIgniter\Config\Config;
//use Config\Services;

/**
*  
*/
use App\Libraries\Encryption;

class Access {
    public function __construct()
    {
        $this->session      = \Config\Services::session();
        $this->encryption   = new Encryption();
        $this->member       = new \App\Models\Member();
    }
    public function signin($username=null,$password=null)
    {
        $result = false;
        $session = session();
        if(!empty($username) && isset($password)){
            $password = $this->encryption->oneway($password);
            $member = $this->member->signin($username,$password);
            if($member){
                $sess_data['signedin'] = $member;
                $session->set($sess_data);
                $result = $member;
            }
        }
        return $result;
    }
}