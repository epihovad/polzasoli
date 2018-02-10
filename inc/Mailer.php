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
		$this->Username = 'info@estill.ru';
		$this->Password = 'N3eE5rVS';
		$this->setFrom('info@estill.ru', 'EUROSTYLE - стильно и тепло');
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

/*$mail = new Mailer();
$mail->Subject = 'Тема';
$mail->setFrom('olap@vseinstrumenti.ru', 'VEGA report');
$mail->addAddress('alexander.epihov@vseinstrumenti.ru', 'Епихов Александр');
$mail->Body = 'тело письма';
$mail->AltBody = 'какой-то AltBody';

//send the message, check for errors
if (!$mail->send()) {
	echo "Mailer Error: " . $mail->ErrorInfo;
} else {
	echo "Message sent!";
}*/