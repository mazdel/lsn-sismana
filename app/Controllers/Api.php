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
	public function signup()
	{
		$member = new \App\Models\Member();
		$data	= [
			'status'	=> false,
			'response'	=> 'Tidak ada data yang terkirim'
		];
		$this->validation->setRules(
			[
				'nik'	=>[
					'label'	=>'NIK',
					'rules'	=>'required|is_natural|min_length[16]|max_length[16]|is_registered'
				],
				'nama'	=>[
					'label'	=>'Nama',
					'rules'	=>'required|alpha_space'
				],
				'password'	=>[
					'label'	=>'Kata sandi',
					'rules'	=>'required'
				],
				'passwordConf'	=>[
					'label'	=>'Konfirmasi kata sandi',
					'rules'	=>'required|matches[password]'
				],
				'telp'	=>[
					'label'	=>'No. Telepon',
					'rules'	=>'required'
				],
			],
			[
				'nik'	=> [
					'required'		=> '{field} harus diisi',
					'min_length'	=> '{field} harus berjumlah {param} digit',
					'max_length'	=> '{field} harus berjumlah {param} digit',
					'is_natural'	=> '{field} harus berupa angka',
					'is_registered'	=> '{field} sudah terdaftar'
				],
				'nama'	=> [
					'required'		=> '{field} harus diisi',
					'alpha_space'	=> '{field} hanya boleh diisi dengan nama yang valid',
				],
				'password' => [
					'required'	=> '{field} harus diisi',
				],
				'passwordConf' => [
					'required'	=> '{field} harus diisi',
					'matches'	=> '{field} harus sama dengan {param}'
				],
				'telp'	=>[
					'required'	=> '{field} harus diisi',
				]
			]
		);
		
		$request_data 		= $this->request->getJSON(true);
		$response = $request_data;
		if(!empty($request_data)){
			$data['status'] = false;
			$data['response'] = $request_data;
			if($this->validation->run($request_data)){
				//remove passwordConf from request
				unset($request_data['passwordConf']);
				
				$result = $member->add($request_data);
				if($result['status']=true){
					$data['status'] = true;
					$data['response'] = $result['data'];
				}
				else{
					$data['response']['error'] = $result['data'];
				}
				
			}
			else{
				$data['response'] 	= $this->validation->getErrors();
			}
		}
		return $this->response->setJSON($data);
	}
	public function signin()
	{
		
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
					'required'	=> '{field} harus diisi',
				],
				'password' => [
					'required'	=> '{field} harus diisi',
				]
			]
		);
		
		$request_data 		= $this->request->getJSON(true);
		
		if(!empty($request_data)){
			$data['status'] = false;
			$data['response'] = $request_data;
			if($this->validation->run($request_data)){
				$result = $this->access->signin($request_data['username'],$request_data['password']);
				$data['response'] = $result['data'];
				if($result['status']==true){
					$data['status'] 	= true;
					$data['response'] 	= 'Selamat Datang '.(!empty($result['data']['nama'])?$result['data']['nama']:$result['data']['username']);
					$data['signedin'] 	= session()->signedin;
				}
			}
			else{
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
