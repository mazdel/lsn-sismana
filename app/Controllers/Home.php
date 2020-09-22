<?php namespace App\Controllers;

use App\Libraries\Access;
use App\Libraries\Encryption;

class Home extends BaseController
{
	function __construct()
	{
		
		$this->config	= config('App',false);
		$this->access	= new Access();
		$this->encrypt 	= new Encryption();
		$this->session 	= service('session');
		$this->data		= session()->get();
		
	}
	public function index()
	{
		$data['debug']=$this->data;
		//return redirect()->to('lsn/');
		//return $this->response->setJSON($data)->setHeader('Location',base_url('lsn/'));
		return view('lsn/index',$data);
	}
	public function install()
	{

		$dbcreator = new \App\Models\Dbcreator();
		
		$data['debug'][] = $dbcreator->create();
		return $this->response->setJSON($data);
	}
	//--------------------------------------------------------------------

}
