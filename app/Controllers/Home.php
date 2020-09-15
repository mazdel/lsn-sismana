<?php namespace App\Controllers;

use App\Libraries\Access;
use App\Libraries\Encryption;

class Home extends BaseController
{
	function __construct()
	{
		
		$this->config 	= config('App',false);
		$this->access	= new Access();
		$this->encrypt 	= new Encryption();
		
		
	}
	public function index()
	{
		$data['debug'][] = $this->encrypt->twoway('Q3VjblhkT1p0R1prazl2RjJMaXBvZz09','d');
		$data['debug'][] = $this->access->test();
		$data['debug'][] = '';
		return view('welcome_message',$data);
	}
	public function install()
	{
		$dbcreator = model('dbcreator');
		
		//$data['debug'][] = $dbcreator->select()->getResultArray();
		//return view('welcome_message',$data);
	}
	//--------------------------------------------------------------------

}
