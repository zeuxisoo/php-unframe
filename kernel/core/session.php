<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Session {

	private static $instance = null;

	public static function init() {
		if (self::$instance === null) {
			self::$instance = new Session();
			session_start();
		}
	}

	public static function set($name, $value) {
		$_SESSION[$name] = $value;
	}

	public static function get($name, $clear = false) {
		$value = Request::session($name);

		if (isset($_SESSION[$name]) === true && $clear === true) {
			unset($_SESSION[$name]);
		}

		return $value;
	}

}
