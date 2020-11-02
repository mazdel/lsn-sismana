<?php namespace App\Controllers;

use App\Libraries\Access;
use App\Libraries\Encryption;


class Anggota extends BaseController
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
		if(empty($this->session->signedin) OR $this->session->signedin['level']!='anggota')
		{
			exit('Error: no session');
		}

	}
	public function index()
	{
		$data['response'] = 'Selamat datang di segment Anggota';
		return $this->response->setJSON($data);
	}

	public function editprofil()
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
					'rules'	=>'required|is_natural|min_length[16]|max_length[16]'
				],
				'tempat_tgl_lahir'	=>[
					'label'	=>'Tempat, Tanggal Lahir',
					'rules'	=>'required'
				],
				'nama'	=>[
					'label'	=>'Nama',
					'rules'	=>'required|alpha_space'
				],
				'telp'	=>[
					'label'	=>'No. Telepon',
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
				],
				'tempat_tgl_lahir'	=> [
					'required'		=> '{field} harus diisi'
				],
				'nama'	=> [
					'required'		=> '{field} harus diisi',
					'alpha_space'	=> '{field} hanya boleh diisi dengan nama yang valid',
				],
				'telp'	=>[
					'required'	=> '{field} harus diisi',
				]
			]
		);
		
		$request_data 		= $this->request->getJSON(true);
		
		if(!empty($request_data)){
			$data['status'] = false;
			$data['response'] = $request_data;
			if($this->validation->run($request_data)){
				if(isset($request_data['resetpass'])){
					$resetpass = $request_data['resetpass'];
					if($resetpass=="Y"){
						$request_data['password']=null;
					}
				}
				//remove needless field from request
				unset($request_data['resetpass']);
				unset($request_data['passwordConf']);

				if(empty($request_data['password'])){
					unset($request_data['password']);
				}
				$idmember = $this->session->signedin['id'];
				$result = $member->edit($idmember,$request_data);
				if($result['status']==true){
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
	public function fotoprofil()
	{
		$member = new \App\Models\Member();
		$data	= [
			'status'	=> false,
			'response'	=> 'Tidak ada data yang terkirim'
		];
		$signedin = $this->session->signedin;
		$validated = $this->validate([
			'foto_profil' => 'uploaded[foto_profil]|mime_in[foto_profil,image/jpg,image/jpeg,image/png]|max_size[foto_profil,4096]'
		]);
		$idmember = $signedin['id'];
		$foto_profil = $this->request->getFile('foto_profil');

		$memberName = $signedin['username']?$signedin['username']:$signedin['nik'];
		$randomName = $foto_profil->getRandomName();
		
		$data['name'] = $memberName.'-'.$randomName;
		//$data['ext'] = $foto_profil->getExtension();
		//$data['mime'] = $foto_profil->getType();
		//$data['session'] = $this->session->signedin;
		
		if($validated){
			$uploadPath = 'src/img/foto/';
			$moveData=$foto_profil->move($uploadPath,$data['name']);
			if($moveData){
				$update = ['foto_profil'=>$uploadPath.$data['name']];
				$result = $member->edit($idmember,$update);
				if($result['status']==true){
					$data['status']=true;
					$data['response']=['uploaded'=>$uploadPath.$data['name']];
				}
			}
		}
		return $this->response->setJSON($data);
	}
	public function getsession()
	{
		$data['signedin']=$this->session->signedin;
		return $this->response->setJSON($_SESSION);
	}
	public function signout()
	{
		$data['status'] 	= false;
		$data['response']	= 'Gagal sign out';
		session_destroy();
		unset($_SESSION);
		if(empty(session()->signedin)){
			$data['status'] 	= true;
			$data['response']	= 'Signed Out';
		}
		$data['sess']	= $_SESSION;
		return $this->response->setJSON($data);
	}
	
}
