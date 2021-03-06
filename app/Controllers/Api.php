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
	public function getdashboard()
	{
		$data['status'] 	= false;
		$data['response']	= 'no data';
		$member = new \App\Models\Member();
		$data = $member->getdata();
		return $this->response->setJSON($data);
	}
	public function getsession()
	{
		$data['signedin']=session('signedin');
		/* agar selalu uptodate */
		if(empty($data['signedin']['id'])){
			return $this->response->setJSON($data);
		}
		$member = new \App\Models\Member();
		$update = $member->show('id',$data['signedin']['id']);
		foreach ($update as $key => $value) {
			if(empty($value['password'])){
				$update[$key]['password'] = false;
			}else{
				$update[$key]['password'] = true;
			}
		}
		$data['signedin'] = $update[0];
		session()->set($data);
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
			/*rules */
			[
				'nik'	=>[
					'label'	=>'NIK',
					'rules'	=>'required|is_natural|min_length[16]|max_length[16]|isnik_exist'
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
				'alamat'	=>[
					'label'	=>'Alamat',
					'rules'	=>'required'
				],
				'tempat_tgl_lahir'	=>[
					'label'	=>'Tempat, Tanggal Lahir',
					'rules'	=>'required'
				],
			],
			/*rules messages */
			[
				'nik'	=> [
					'required'		=> '{field} harus diisi',
					'min_length'	=> '{field} harus berjumlah {param} digit',
					'max_length'	=> '{field} harus berjumlah {param} digit',
					'is_natural'	=> '{field} harus berupa angka',
					'isnik_exist'	=> '{field} sudah terdaftar, silahkan untuk menggunakan {field} lainnya'
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
				],
				'alamat'	=> [
					'required'		=> '{field} harus diisi',
				],
				'tempat_tgl_lahir'	=> [
					'required'		=> '{field} harus diisi'
				],
			]
		);
		
		$request_data 		= $this->request->getJSON(true);
		
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
					$data['redirect'] = '#signin';
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
					'rules'	=>'required|ismember_exist'
				],
				'password'	=>[
					'label'	=>'Kata sandi',
					'rules'	=>'required|ispassword_right[username]'
				]
			],
			[
				'username'	=> [
					'required'			=> '{field} harus diisi',
					'ismember_exist'	=> '{field} belum terdaftar'
				],
				'password' => [
					'required'			=> '{field} harus diisi',
					'ispassword_right'	=>	'{field} untuk {param} tidak tepat'
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
					$data['redirect'] 	= 'main/dashboard';
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
		session_destroy();
		unset($_SESSION);
		if(empty(session()->signedin)){
			$data['status'] 	= true;
			$data['response']=array(
				"message"	=> "signed out",
				"redirect"	=> ''
			);
		}
		return $this->response->setJSON($data);
	}
	
}
