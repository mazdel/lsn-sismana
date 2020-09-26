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
		//$this->data['response']	= '';
		helper(['url']);

	}
	public function index()
	{
		$data['response'] = 'Nothing to see here :p';
		return $this->response->setJSON($data);
	}
	public function signin()
	{
		//$data	= $this->data;
		$member = new \App\Models\Member();
		$data	= [
			'status'	=> false,
			'response'	=> 'Tidak ada data yang terkirim'
		];
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
		
		$request_data 		= $this->request->getJSON(true);
		
		if(!empty($request_data)){
			$data['status'] = false;
			$data['response'] = $request_data;
			if($this->validation->run($request_data,'signin')){
				$result = $this->access->signin($request_data['username'],$request_data['password']);
				$data['response'] = $result['data'];
				if($result['status']==true){
					$data['status'] 	= true;
					$data['response'] 	= 'Selamat Datang '.(!empty($result['data']['nama'])?$result['data']['nama']:$result['data']['username']);
					$data['signedin'] 	= session()->signedin;
				}
			}
			else{
				$data['status']		= 'error';
				$data['response'] 	= $this->validation->getErrors();
			}
		}
		return $this->response->setJSON($data);
	}
	public function signout()
	{
		$data['status'] 	= false;
		$data['response']	= 'Gagal sign out';
		unset($_SESSION);
		if(empty(session()->signedin)){
			$data['status'] 	= true;
			$data['response']	= 'Signed Out';
		}
		return $this->response->setJSON($data);
	}
}
