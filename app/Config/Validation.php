<?php namespace Config;

class Validation
{
	//--------------------------------------------------------------------
	// Setup
	//--------------------------------------------------------------------

	/**
	 * Stores the classes that contain the
	 * rules that are available.
	 *
	 * @var array
	 */
	public $ruleSets = [
		\CodeIgniter\Validation\Rules::class,
		\CodeIgniter\Validation\FormatRules::class,
		\CodeIgniter\Validation\FileRules::class,
		\CodeIgniter\Validation\CreditCardRules::class,
		\App\Validation\CustomRules::class,
	];

	/**
	 * Specifies the views that are used to display the
	 * errors.
	 *
	 * @var array
	 */
	public $templates = [
		'list'   => 'CodeIgniter\Validation\Views\list',
		'single' => 'CodeIgniter\Validation\Views\single',
	];

	//--------------------------------------------------------------------
	// Rules
	//--------------------------------------------------------------------
	public $signin = [
		'username'	=>[
			'label'	=>'Nama/NIK/No.telp anggota',
			'rules'	=>'required'
		],
		'password'	=>[
			'label'	=>'Kata sandi',
			'rules'	=>'required'
		]
	];
	public $signin_errors = [
		'username'	=> [
			'required'	=> '{field} harus di isi',
		],
		'password' => [
			'required'	=> '{field} harus di isi',
		]
	];
}
