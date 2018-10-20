<?php

require_once 'Create_Form.php';
class Success_Form extends Create_Form
{
	
	function __construct()
	{
		$this->_create = new Create_Form();
	}

	public function form( $form_name, $dataPost=null ){

		switch ($form_name) {
			case 'login':
				return $this->login_form( $dataPost );
				break;
		}
	}

	private function login_form( $dataPost ){

		return $this->_create
			->url(URL."login?login_attempt=1")
			->method('post')
			->addClass('login_form')

			->field("email")
				->addClass('inputtext')
				->placeholder("ชื่อผู้ใช้หรืออีเมล")
				->value( isset($dataPost['post']['email'])?$dataPost['post']['email']:"" )
				->autofocus()

			->field("pass")
				->type('password')
				->addClass('inputtext')
				->placeholder("รหัสผ่าน")
				->notify( isset($dataPost['error']['pass'])?$dataPost['error']['pass']:"" )

			->submit()->addClass("submit btn btn-blue")->value("เข้าสู่ระบบ")
			->html();

	}



}