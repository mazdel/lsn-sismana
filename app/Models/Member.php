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
        $encryption = new \App\Libraries\Encryption();
        $default_password = config('Sismana',false)->default_password;
        $pre_result=[];
        $result = false;
        
        if(!empty($username) && isset($password)){    
            $this->member->groupStart()
                            ->where('username',$username)
                            ->orWhere('nik',$username)
                            ->orWhere('telp',$username)
                        ->groupEnd();
            if($this->ishave_password($username) || $password!=$default_password){
                $password=$encryption->oneway($password);
                $this->member->where('password',$password);
            }
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
     * @param  string $sort asc|dsc
     * @param  int $limit 
     * @param  int $page
     * @return array
     */
    public function show($find='all',$data=null,$method='normal',$sort='asc',$limit=null,$page=0)
    {
        switch($find){
            case 'all':
                $sortBy = 'nama';
                break;
            default:
                $sortBy = $find;
                $this->member->where([$find=>$data]);
                break;
        }
        if (!empty($limit) && !empty($page)) {
            $offset = $limit * ($page - 1);
            $this->member->limit($limit,($offset<0?0:$offset));
        }
        switch ($method) {
            case 'normal':
                $this->member->where([
                    'active'    =>'Y',
                    'deleted'   =>'N'
                    ]);
                break;
            case 'count':
                $this->member->getWhere(['deleted' => 'N']);
                return $this->member->countAllResults();
                break;
            case 'recursive':
                break;
        }
        $this->member->orderBy($sortBy,$sort);
        return $this->member->get()->getResultArray();
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
        if(!empty($data['password']) || strlen($data['password'])>0){
            $data['password'] = $encryption->oneway($data['password']);
        }else{
            $data['password'] = NULL;
        }
        $result['data'] = $this->member->insert($data);
        $dberror = $this->db->error();
        if($dberror['code']>0){
            $result['data']     = $dberror;
            $result['status']   = false;
        }else{
            $result['status'] = true;
        }
        return $result;
    }    
    /**
     * edit
     *
     * @param int $id id member
     * @param mixed $data
     * @return array
     */
    public function edit(int $id=null,$data=null)
    {
        $encryption  =  new \App\Libraries\Encryption();

        if(empty($data) || empty($id)){
            $result['data']     = 'no data';
            $result['status']   = false;
            return $result;
        }
        if(isset($data['password'])){
            if(!empty($data['password']) || strlen($data['password'])>0){
                $data['password'] = $encryption->oneway($data['password']);
            }else{
                $data['password'] = NULL;
            }
        }
        $this->member->where('id',$id);
        $result['data'] = $this->member->update($data);
        
        $dberror = $this->db->error();
        if($dberror['code']>0){
            $result['data']     = $dberror;
            $result['status']   = false;
        }else{
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
    /**
     * ishave_password
     * check is member have set a password or not
     * @param  string $username
     * @return boolean
     */
    public function ishave_password($username)
    {
        $result = false;
        if(!empty($user)){    
            $this->member->groupStart()
                            ->where('username',$user)
                            ->orWhere('nik',$user)
                            ->orWhere('telp',$user)
                        ->groupEnd();
            $password =  $this->member->get()->getRowArray()['password'];
            $result = !empty($password)?true:false;
        }
        return $result;
    }
}