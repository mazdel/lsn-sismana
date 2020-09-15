<?php namespace App\Libraries;

/**
*  
*/
class Encryption {
    public function __construct()
    {
        $this->encryption = config('App',false)->encryption;
        helper('encoder');
    }
    public function oneway($data=null)
    {
        if (!empty($data)) {
			return md5($this->encryption['secret'].$data);
		}
		return false;
    }
    public function twoway($data=null,$action='e',$s_key=null,$s_iv=null)
	{
		if (!empty($data)) {
			if (empty($s_key)) {
				$s_key = $this->encryption['secret'];
			}
			if (empty($s_iv)) {
				$s_iv = $this->encryption['iv'];
			}
			$encrypt_method = 'AES-256-CBC';
			$key = hash('sha256', $s_key);
			$iv = substr(hash('sha256', $s_iv), 0,16);
			switch ($action) {
				case 'e':
					return base64_safeurl(openssl_encrypt($data, $encrypt_method, $key,0,$iv),'e');
					break;
				case 'd':
					return openssl_decrypt(base64_safeurl($data,'d'), $encrypt_method, $key,0,$iv);
					break;
			}
		}
		return false;
	}
}