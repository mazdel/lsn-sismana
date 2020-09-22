<?php namespace App\Controllers;

use App\Libraries\Access;
use App\Libraries\Encryption;

class Api extends BaseController
{
	function __construct()
	{
		
		$this->config		= config('App',false);
		$this->access		= new Access();
		$this->encrypt 		= new Encryption();
		$this->session 		= \Config\Services::session();
		$this->validation	= \Config\Services::validation();
		$this->data			= session()->get();
		//$this->data['message']	= '';
		helper(['url']);

	}
	public function index()
	{
		$data['message'] = 'Nothing to see here :p';
		return $this->response->setJSON($data);
	}
	public function signin()
	{
		$data	= $this->data;
		$member = new \App\Models\Member();
		
		$this->validation->setRules(
			[
				'username'	=>[
					'label'	=>'Nama/NIK/No.telp anggota',
					'rules'	=>'required'
				],
				'password'	=>[
					'label'	=>'Kata sandi',
					'rules'	=>'required'
				]
			],
			[
				'username'	=> [
					'required'	=> '{field} dibutuhkan',
				],
				'password' => [
					'required'	=> '{field} dibutuhkan',
				]
			]
		);
		$request_data 		= $this->request->getPost();
		
		if(!empty($request_data)){
			if($this->validation->run($request_data,'signin')){
				$data['status']		= 'success';
				$result = $this->access->signin($request_data['username'],$request_data['password']);
				$data['message']	= 'Selamat datang '.$result['nama'];
			}
			else{
				$data['status']		= 'error';
				$data['message'] 	= $this->validation->getErrors();
			}
		}
		return $this->response->setJSON($data);
	}
}
