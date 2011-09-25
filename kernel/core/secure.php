<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Secure {

	const NOT_SET_CSRF_ENCRYPT_KEY = -1;

	private static $csrf_token_ttl	= 7200;
	private static $csrf_encrypt_key= '';

	public static function init($settings) {
		foreach($settings as $key => $value) {
			self::$$key = $value;
		}
	}

	public static function add_slash($string, $force = 0, $strip = false) {
		if(get_magic_quotes_gpc() == false || $force) {
			if(is_array($string)) {
				foreach($string as $key => $val) {
					$string[$key] = self::add_slash($val, $force, $strip);
				}
			} else {
				$string = addslashes($strip === true ? stripslashes($string) : $string);
			}
		}
		return $string;
	}

	public static function encrypt_text($text, $csrf_encrypt_key = '') {
		if (empty($csrf_encrypt_key) === true && empty(self::$csrf_encrypt_key) === true) {
			self::error(self::NOT_SET_CSRF_ENCRYPT_KEY);
		}else{
			if (empty($csrf_encrypt_key) === false) {
				return hash_hmac('md5', $text, $csrf_encrypt_key);
			}else{
				return hash_hmac('md5', $text, self::$csrf_encrypt_key);	
			}
		}
	}

	public static function generate_csrf_token($url = '') {
		$i = ceil(time() / self::$csrf_token_ttl);
		return substr(self::encrypt_text($i.$url), -22, 12);
	}
	
	public static function validate_csrf_token($token, $url = '') {
		$i = ceil(time() / self::$csrf_token_ttl);
		return substr(self::encrypt_text($i.$url), -22, 12) == $token || substr(self::encrypt_text(($i - 1).$url), -22, 12) == $token;
	}

	private static function error($type) {
		switch($type) {
			case self::NOT_SET_CSRF_ENCRYPT_KEY:
				$message = "Not setup csrf encrypt key";
				break;
		}
		exit(sprintf("<strong>[Error]:</strong> %s", $message));
	}
}
?>