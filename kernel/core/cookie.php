<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Cookie {

	public static function get($name) {
		return Request::cookie($name);
	}

	public static function set($name, $value, $time_out = 3600, $path = '/', $domain = '') {
		if (empty($domain) === true) {
			$domain = $_SERVER['HTTP_HOST'];
		}

		if (in_array($domain, array('localhost', '127.0.0.1')) === false && self::get_client_ip() != "127.0.0.1") {
			if (strtolower(substr($domain, 0, 4)) == 'www.') {
				$domain = substr($domain, 4);
			}
	      	$domain = '.'.$domain;
      	}else{
      		$domain = "";
      	}

		setcookie($name, $value, $time_out, $path, $domain, ($_SERVER['SERVER_PORT'] == 443 ? 1 : 0));
	}

	public static function remove($name) {
		self::add($name, '', -84600);

		if (isset($_COOKIE[$name])) {
			unset($_COOKIE[$name]);
		}
	}

}
?>