<?php
if (defined('IN_APP') === false) exit('Access Dead');

class Cache {

	private static $adapter = null;

	public static function init($settings) {
		self::$adapter = $settings['adapter'];
	}

	public static function get_adapter() {
		return self::$adapter;
	}

	public static function add($name, $value) {
		return self::$adapter->add($name, $value);
	}

	public static function set($name, $value) {
		self::$adapter->set($name, $value);
	}

	public static function get($name) {
		return self::$adapter->get($name);
	}

	public static function delete($name) {
		return self::$adapter->delete($name);
	}

	public static function clear() {
		return self::$adapter->clear();
	}
}
?>