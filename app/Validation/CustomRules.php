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
}