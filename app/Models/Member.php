<?php namespace App\Models;

use CodeIgniter\Model;

class Member extends Model{
    public function __construct()
    {
        $this->db = db_connect(null,false);
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
        $member = $this->db->table('member');
        switch($find){
            case 'all':
                break;
            default:
                $member->where([$find=>$data]);
                break;
        }
        switch ($method) {
            case 'normal':
                $member->where([
                    'active'    =>'Y',
                    'deleted'   =>'N'
                    ]);
                break;
            case 'count':
                $offset = $limit * ($page - 1);
                if (!$pagination) {
                    $limit=null; $offset=0;
                }
                $member->getWhere(['deleted' => 'N'],$limit,$offset);
                return $member->countAllResults();
                break;
            case 'recursive':
                break;
        }
        if ($pagination) {
            $offset = $limit * ($page - 1);
            $member->limit($limit,$offset);
        }
        return $member->get()->getResultObject();
    }
    public function add($data=null)
    {
        return false;
    }
}