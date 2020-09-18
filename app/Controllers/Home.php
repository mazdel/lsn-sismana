<?php namespace App\Controllers;

use App\Libraries\Access;
use App\Libraries\Encryption;

class Home extends BaseController
{
	function __construct()
	{
		
		$this->config	= config('App',false);
		$this->access	= new Access();
		$this->encrypt = new Encryption();
		
		
	}
	public function index()
	{
		$member = new \App\Models\Member();
		$data['debug'][] = $member->show('all',null,'count');
		$data['debug'][] = $this->encrypt->twoway('Q3VjblhkT1p0R1prazl2RjJMaXBvZz09','d');
		$data['debug'][] = $this->access->test();
		$data['debug'][] = base_url();
		return $this->response->setJSON($data);
		//return view('welcome_message',$data);
	}
	public function install()
	{

		$dbcreator = new \App\Models\Dbcreator();
		$member = new \App\Models\Member();
		$data['debug'][] = $dbcreator->create();
		return $this->response->setJSON($data);
		//return view('welcome_message',$data);
	}
	//--------------------------------------------------------------------

}
