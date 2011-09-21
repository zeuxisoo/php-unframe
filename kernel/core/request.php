<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Request {
	
	public static function get($key, $default = "") {
		return self::read_from($_GET, $key, $default);
	}

	public static function post($key, $default = "") {
		return self::read_from($_POST, $key, $default);	
	}

	public static function cookie($key, $default = "") {
		return self::read_from($_COOKIE, $key, $default);
	}

	public static function session($key, $default = "") {
		return self::read_from($_SESSION, $key, $default);
	}

	public static function file($key) {
		return self::read_from($_FILES, $key);
	}

	public static function gpc($key, $default = "") {
		return self::read_from($_REQUEST, $key, $default);
	}

	public static function env($key, $default = "") {
		return self::read_from($_ENV, $key, $default);
	}

	public static function server($key, $default = "") {
		return self::read_from($_SERVER, $key, $default);
	}

	public static function read_from($container, $key, $default = "") {
		return isset($container[$key]) === true ? $container[$key] : $default;
	}

	public static function is_post() {
		return strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';
	}
	
	public static function is_get() {
		return strtoupper($_SERVER['REQUEST_METHOD']) == 'GET';
	}

	public static function is_ajax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) === true && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}

}
?>