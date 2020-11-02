<?php namespace App\Controllers;

use App\Libraries\Access;
use App\Libraries\Encryption;

class Main extends BaseController
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
		if(!empty($this->session->signedin))
		{
			return redirect()->to('main/dashboard');
		}
		$data=[];
		//$data['debug']=$this->data;
		return view('lsn/index',$data);
	}
	public function dashboard()
	{
		if(empty($this->session->signedin) /*OR $this->session->signedin['level']!='admin'*/)
		{
			return redirect()->route('main');
		}
		$data=[];
		$data['debug']=$this->data;
		return view('lsn/dashboard',$data);
	}
	public function signout()
	{
		session_destroy();
		unset($_SESSION);
		return redirect()->route('main');
	}
	public function install()
	{
		$dbcreator = new \App\Models\Dbcreator();
		$data['debug'][]='done';
		if($dbcreator->tb_check()!=true){
			$data['debug'][] = $dbcreator->create();
		}
		return $this->response->setJSON($data);
	}
	//--------------------------------------------------------------------

}
