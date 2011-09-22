<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Email {

	private static $settings = array();

	public static function init($settings) {
		self::$settings = $settings;
	}
	
	public static function valid($email) {
		return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
	}

	public static function send($contents, $attachment = array()) {
		$to_mail = $contents['to_mail'];
		$subject = $contents['subject'];
		$message = $contents['message'];
		$to_user = isset($contents['to_user']) ? $contents['to_user'] : "Dear Sir/Madam";

		import("kernel.library.phpmailer.*");

		$mail = new PHPMailer();

		if (self::$settings['smtp_enable'] === true) {
			$mail->IsSMTP();

			if (self::$settings['smtp_secure'] != '') {
				$mail->SMTPSecure = self::$settings['smtp_secure'];
			}

			$mail->SMTPAuth = self::$settings['smtp_auth'];
			$mail->Host     = self::$settings['smtp_host'];
			$mail->Username = self::$settings['smtp_user'];
			$mail->Password = self::$settings['smtp_pass'];
			$mail->Port     = self::$settings['smtp_port'];
		}

		$mail->IsHTML(true);
		$mail->CharSet  = self::$settings['charset'];
		$mail->From     = self::$settings['from_address'];
		$mail->FromName = self::$settings['from_username'];
		$mail->Subject  = $subject;
		$mail->Body     = $message;

		$mail->AddAddress($to_mail, $to_user);

		if (is_array($attachment) === true && empty($attachment) === false) {
			foreach($attachment as $files) {
				$mail->AddAttachment($files);
			}
		}

		return $mail->Send();
	}

}
?>