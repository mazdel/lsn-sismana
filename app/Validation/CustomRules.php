<?php namespace App\Validation;

use App\Models\Member;


class CustomRules
{
    ///custom validator
    public function __construct()
    {
        
    }
	public function is_registered(string $user): bool
	{
        $member = new \App\Models\Member();
        /* cek user jika sudah terdaftar, maka validator mengembalikan respon false
         * 
        */
		if($member->is_registered($user)){
			return false;
		}
		return true;
    }
    public function ismember_exist($user)
    {
        $member = new \App\Models\Member();
        /*
        * cek apakah member dengan user tersebut sudah ada/belum
        * sebenernya sama dengan is_registered, cuma hasilnya di balik
        */
        if($member->is_registered($user)){
            return true;
        }
        return false;
    }
    public function ispassword_right($password = null,string $userfield,$data)
    {
        $member = new \App\Models\Member();
        $result = false;

        if(empty($password)){
            return false;
        }
        if($member->signin($data[$userfield],$password)){
            $result = true;
        }
        return $result;
    }
}