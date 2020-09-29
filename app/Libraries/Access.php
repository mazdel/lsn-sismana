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
        $session            = session();
        $result['status']   = false;
        $result['data']     = 'Pengguna dengan kata sandi tersebut tidak ditemukan';

        if(!empty($username) && isset($password)){
            $password = $this->encryption->oneway($password);
            $memberIn = $this->member->signin($username,$password);
            if($memberIn){
                unset($memberIn['password']);
                unset($memberIn['active']);
                unset($memberIn['deleted']);
                $sess_data['signedin']  = $memberIn;
                $session->set($sess_data);
                $result['status']       = true;
                $result['data']         = $memberIn;
            }
        }
        return $result;
    }
}