<?php namespace App\Controllers;

use App\Libraries\Access;
use App\Libraries\Encryption;


class Admin extends BaseController
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
		if(empty($this->session->signedin) OR $this->session->signedin['level']!='admin')
		{
			exit('Error: no session');
		}

	}
	public function index()
	{
		$data['response'] = 'Selamat datang di segment admin';
		return $this->response->setJSON($data);
	}

	public function getmember()
	{
		$member = new \App\Models\Member();
		$search = "all";$data=null;$count=25;$page=1;
		$request_data = $this->request->getJSON(true);
		$this->validation->setRules([
			/**rules */
			'page'	=>[
				'label'	=>'halaman',
				'rules'	=>'is_natural_no_zero'
			],
			'count'	=>[
				'label'	=>'jumlah data',
				'rules'	=>'is_natural_no_zero'
			]
		]);
		if(!empty($request_data)){
			if($this->validation->run($request_data)){
				$count=$request_data['count'];
				$page=$request_data['page'];
			}
		}
		$pre_result = $member->show($search,$data,'normal','asc',$count,$page);
		$result = $pre_result;
		foreach ($pre_result as $key => $value) {
			if(empty($value['password'])){
				$result[$key]['password']=false;
			}else{
				$result[$key]['password']=true;
			}
			
		}
		$data['paging']['dataLength']=$member->show('all',null,'count');
		$data['paging']['dataCount']=$count;
		$data['paging']['page']=$page;
		$data['paging']['pages']=ceil($data['paging']['dataLength'] / $count);
		$data['response']= $result;
		return $this->response->setJSON($data);
	}
	public function addmember()
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
					'rules'	=>'required|is_natural|min_length[16]|max_length[16]|is_registered'
				],
				'nama'	=>[
					'label'	=>'Nama',
					'rules'	=>'required|alpha_space'
				],
				'passwordConf'	=>[
					'label'	=>'Konfirmasi kata sandi',
					'rules'	=>'matches[password]'
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
					'is_registered'	=> '{field} sudah terdaftar, silahkan untuk menggunakan {field} lainnya'
				],
				'nama'	=> [
					'required'		=> '{field} harus diisi',
					'alpha_space'	=> '{field} hanya boleh diisi dengan nama yang valid',
				],
				'passwordConf' => [
					
					'matches'	=> '{field} harus sama dengan kata sandi'
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
				//remove passwordConf from request
				unset($request_data['passwordConf']);
				
				$result = $member->add($request_data);
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
	public function editmember($idmember)
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
					'is_natural'	=> '{field} harus berupa angka'
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
		//return $this->response->setJSON($request_data);
		if(!empty($request_data)){
			$data['status'] = false;
			$data['response'] = $request_data;
			if($this->validation->run($request_data)){
				$resetpass = $request_data['resetpass'];
				//remove needless field from request
				unset($request_data['resetpass']);
				unset($request_data['passwordConf']);

				if($resetpass=="Y"){
					$request_data['password']=null;
				}
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
