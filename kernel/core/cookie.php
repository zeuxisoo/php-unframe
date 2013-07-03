<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Cookie {

	public static function get($name) {
		return Request::cookie($name);
	}

	public static function set($name, $value, $time_out = 3600, $path = '/', $domain = '') {
		setcookie($name, $value, $time_out, $path, $domain, ($_SERVER['SERVER_PORT'] == 443 ? 1 : 0));
	}

	public static function remove($name) {
		self::set($name, '', -84600);

		if (isset($_COOKIE[$name])) {
			unset($_COOKIE[$name]);
		}
	}

}
