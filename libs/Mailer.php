<?php

require 'Mailer/class.phpmailer.php';
require 'Mailer/Form_Mailer.php';

class Mailer
{
	function __construct()
	{
		$this->mail = new PHPMailer(true);
		$this->from = new Form_Mailer();

		$this->mail->CharSet = "utf-8";
		$this->mail->IsSMTP();
		$this->mail->SMTPDebug = 0;
		$this->mail->SMTPAuth = true;

		$this->mail->Host = MAIL_HOST;
		$this->mail->Username = MAIL_USER;
		$this->mail->Password = MAIL_PASS;

		$this->mail->SetFrom(MAIL_USER, MAIL_NAME);
		$this->mail->AddReplyTo(MAIL_USER, MAIL_NAME);
	}

	public function confirmEmail( $data=array() )
	{
		$this->mail->Subject = $data['title'];
		$this->mail->MsgHTML( $this->from->confirmEmail( $data ) );
		$this->mail->AddAddress( $data['email'], $data['name'] );

		return $this->mail->Send();
		// $this->from->confirmEmail();
	}

	public function contact($data)
	{
		$this->mail->Subject = $data['subject'];
		$this->mail->MsgHTML( $this->from->contact( $data ) );
		$this->mail->AddAddress( $data['email'], $data['name'] );

		return $this->mail->Send();
	}
}