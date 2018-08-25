<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/PHPMailer-master/PHPMailerAutoload.php');

class Mailer extends PHPMailer {
	public function __construct() {

		$this->isSMTP();
		$this->CharSet = 'utf-8';
		$this->Port = '25';
		$this->SMTPAuth = true;
		$this->SMTPDebug = 0;
		$this->Debugoutput = 'html';
		$this->isHTML(true);
		$this->Host = 'smtp.timeweb.ru';
		$this->Username = '';
		$this->Password = '';
		$this->setFrom('info@polzasoli.ru', 'Ассоль - соляная пещера');
	}

	// $emails - массив адресатов array('test1@mail.ru'=>'Тест Петрович','test2@mail.ru'=>'Тест Иванович')
	public function mailTo($to,$subject,$body){
		$this->Subject = $subject;
		$this->Body = $body;
		foreach ($to as $email){
			$this->addAddress($email);
		}
		return $this->send();
	}
}