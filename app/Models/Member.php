<?php namespace App\Models;

use CodeIgniter\Model;

class Member extends Model{
    public function __construct()
    {
        $this->db = db_connect(null,false);
        $this->member = $this->db->table('member');
    }   
    /**
     * signin
     *
     * @param  string $username username/nik/telp
     * @param  string $password password of the user
     * @return void
     */
    public function signin($username=null,$password=null)
    {
        $result = false;
        if(!empty($username) && isset($password)){    
            $this->member->groupStart()
                            ->where('username',$username)
                            ->orWhere('nik',$username)
                            ->orWhere('telp',$username)
                        ->groupEnd()
                        ->where('password',$password);
            $pre_result =  $this->member->get()->getRowArray();
            $result = !empty($pre_result)?$pre_result:false;
        }
        return $result;

    }    
        
    /**
     * show users from database
     *
     * @param  string $find what column are you looking for
     * @param  string $data data that you want to find
     * @param  string $method normal/count/recursive
     * @param  boolean $pagination true|false
     * @param  int $limit 
     * @param  int $page
     * @return void
     */
    public function show($find='all',$data=null,$method='normal',$pagination=false,$limit=20,$page=1)
    {
        switch($find){
            case 'all':
                break;
            default:
                $this->member->where([$find=>$data]);
                break;
        }
        switch ($method) {
            case 'normal':
                $this->member->where([
                    'active'    =>'Y',
                    'deleted'   =>'N'
                    ]);
                break;
            case 'count':
                $offset = $limit * ($page - 1);
                if (!$pagination) {
                    $limit=null; $offset=0;
                }
                $this->member->getWhere(['deleted' => 'N'],$limit,$offset);
                return $this->member->countAllResults();
                break;
            case 'recursive':
                break;
        }
        if ($pagination) {
            $offset = $limit * ($page - 1);
            $this->member->limit($limit,$offset);
        }
        return $this->member->get()->getResultObject();
    }    
    /**
     * add
     *
     * @param  array $data array to be inserted into database
     * @return void
     */
    public function add($data=null)
    {
        $encryption  =  new \App\Libraries\Encryption();
        
        if(empty($data)){
            $result['data']     = 'no data';
            $result['status']   = false;
        }
        $data['password'] = $encryption->oneway($data['password']);
        $result['data'] = $this->member->insert($data);
        if($this->db->error()>0){
            $result['data']     = $this->db->error();
            $result['status']   = false;
        }
        else{
            $result['status'] = true;
        }
        return $result;
    }    
    /**
     * is_registered
     *
     * @param  string $user username/NIK/phone to be checked
     * @return void
     */
    public function is_registered($user)
    {
        $result = false;
        if(!empty($user)){    
            $this->member->groupStart()
                            ->where('username',$user)
                            ->orWhere('nik',$user)
                            ->orWhere('telp',$user)
                        ->groupEnd();
            $pre_result =  $this->member->get()->getRowArray();
            $result = !empty($pre_result)?true:false;
        }
        return $result;
    }
}